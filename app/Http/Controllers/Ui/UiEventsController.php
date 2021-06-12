<?php


namespace App\Http\Controllers\Ui;


use App\Models\Alliance;
use App\Models\EventPlayer;
use App\Models\EventSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class UiEventsController extends UiMainController
{
    public function index(Request $request = null, $period = null)
    {
        $period = $period ? $period : 'today';


        $dateFormat = 'Y-m-d';
        $filters = $this->getFilters($request);
        setcookie('filters', json_encode($filters), time() + 3600 * 60 * 30, '/');

        $where = '';
        if ($filters['systemTypes']) {
            $where .= ($where ? " AND " : "") . "l.type IN (" . implode(',', $filters['systemTypes']) . ")";
        }
        if ($filters['moonTh']) {
            $where .= ($where ? " AND " : "") . "(l.type NOT IN (20,22) OR l.threshold >= " . $filters['moonTh'] . ")";
        }
        if ($filters['debrisTh']) {
            $where .= ($where ? " AND " : "") . "(l.type NOT IN (30,31,32,33) OR l.threshold >= " . $filters['debrisTh'] . ")";
        }
        $where .= ($where ? " AND " : "") . self::getPeriodWhere($period);
        $systemEvents = [];
        $rows = DB::select("SELECT l.* FROM ovg_systems_log AS l " . ($where ? " WHERE " . $where : "") . " ORDER BY l.created DESC, l.player_id");
        $events = EventSystem::hydrate($rows);
        $events->load('player');
        foreach ($events as $event) {
            $date = date($dateFormat, strtotime($event->created));
            $playerId = $event->player_id;
            $key = "{$date}_{$playerId}_($event->coords)";
            if (!isset($systemEvents[$key])) {
                $systemEvents[$key] = [
                    'date' => $date,
                    'player' => $event->player,
                    'gal' => $event->gal,
                    'sys' => $event->sys,
                    'pos' => $event->pos,
                    'coords' => $event->coords,
                ];
            }
            $systemEvents[$key]['rows'][] = [
                'type' => $event->type,
                'json' => json_decode($event->json, true),
            ];
        }
        krsort($systemEvents);


        $where = '';
        if ($filters['playerTypes']) {
            $where .= ($where ? " AND " : "") . "l.type IN (" . implode(',', $filters['playerTypes']) . ")";
        }
        if ($filters['rankTh']) {
            $where .= ($where ? " AND " : "") . "p.rank <= " . $filters['rankTh'];
        }
        $where .= ($where ? " AND " : "") . self::getPeriodWhere($period);
        $playerEvents = [];
        $ids = [];
        $alliances = [];
        $whereVac = null;
        if (isset($filters['playerNovac']) && $filters['playerNovac'] == true) {
            $whereVac = "";
            $where .= ($where ? " AND " : "") . "p.v = 0";
        }
        $rows = DB::select("SELECT l.* FROM ogv_players_log AS l
            INNER JOIN ogv_players AS p ON p.id = l.player_id "
            . ($where ? " WHERE " . $where : "")
            . " ORDER BY l.created DESC");
        $events = EventPlayer::hydrate($rows);
        $events->load('player');
        foreach ($rows as $row) {
            if ($row->type == 70) {
                $json = json_decode($row->json, true);
                if ($json['old']) {
                    $ids['ally'][] = $json['old'];
                }
                if ($json['new']) {
                    $ids['ally'][] = $json['new'];
                }
            }
        }
        foreach ($events as $event) {
            $date = date($dateFormat, strtotime($event->created));
            $playerId = $event->player_id;
            if (!isset($playerEvents[$date][$playerId])) {
                $playerEvents[$date][$playerId] = [
                    'date' => $date,
                    'player' => $event->player,
                ];
            }
            $playerEvents[$date][$playerId]['rows'][] = [
                'type' => $event->type,
                'json' => json_decode($event->json, true),
            ];
        }
        krsort($playerEvents);

        // $ids['ally'] = [500085, 500029];
        if (isset($ids['ally'])) {
            $alliances = Alliance::whereIn('id', $ids['ally'])->get()->keyBy('id');
        }

        return view('events', [
            'period' => $period,
            'systemEvents' => $systemEvents,
            'playerEvents' => $playerEvents,
            'alliances' => $alliances,
            'filters' => $filters,
        ]);
    }

    private static function getPeriodWhere($period)
    {
        $where = null;
        if ($period == 'yesterday') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-1 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59", strtotime("-1 days")) . "'";
        } elseif ($period == '2-days') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-2 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59", strtotime("-2 days")) . "'";
        } elseif ($period == '3-days') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-3 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59", strtotime("-3 days")) . "'";
        } elseif ($period == 'last-3-days') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-3 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59") . "'";
        } elseif ($period == 'last-7-days') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-7 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59") . "'";
        } elseif ($period == 'last-30-days') {
            $where = "l.created >= '" . date("Y-m-d 00:00:00", strtotime("-30 days"))
                . "' AND l.created <= '" . date("Y-m-d 23:59:59") . "'";
        } else {
            $where = "l.created >= '" . date("Y-m-d 00:00:00")
                . "' AND l.created <= '" . date("Y-m-d 23:59:59") . "'";
        }

        return $where;
    }

    private function getFilters(Request $request = null)
    {
        $filters = [];

        if (isset($_COOKIE['filters'])) {
            $filters = json_decode($_COOKIE['filters'], true);
        }

        if ($request) {
            $system = null;
            $player = null;
            if ($request->has('filterEvents')) {
                $filters['system'] = (array)$request->get('system');
                $filters['moonTh'] = intval($request->get('moonTh'));
                $filters['debrisTh'] = intval($request->get('debrisTh'));
                $filters['rankTh'] = intval($request->get('rankTh'));
                $filters['player'] = (array)$request->get('player');
                $filters['playerNovac'] = $request->get('playerNovac') == '1' ? true : false;
            }

            $filters['systemTypes'] = $this->getFilterTypes($filters['system']);
            $filters['playerTypes'] = $this->getFilterTypes($filters['player']);
        }

        if (!array_key_exists('system', $filters)) {
            $filters['system'] = [];
        }
        if (!array_key_exists('moonTh', $filters)) {
            $filters['moonTh'] = 0;
        }
        if (!array_key_exists('debrisTh', $filters)) {
            $filters['debrisTh'] = 0;
        }
        if (!array_key_exists('rankTh', $filters)) {
            $filters['rankTh'] = 0;
        }
        if (!array_key_exists('systemTypes', $filters)) {
            $filters['systemTypes'] = [];
        }
        if (!array_key_exists('player', $filters)) {
            $filters['player'] = [];
        }
        if (!array_key_exists('playerTypes', $filters)) {
            $filters['playerTypes'] = [];
        }
        if (!array_key_exists('playerNovac', $filters)) {
            $filters['playerNovac'] = false;
        }

        return $filters;
    }

    private function getFilterTypes($filters)
    {
        $types = [];
        if (is_array($filters)) {
            foreach ($filters as $key => $val) {
                if ((string)intval($val) === (string)$val) {
                    $types[] = (int)$val;
                } elseif (stristr($val, ',')) {
                    foreach (explode(',', $val) as $v) {
                        $types[] = (int)$v;
                    }
                }
            }
        }

        return $types;
    }

}
