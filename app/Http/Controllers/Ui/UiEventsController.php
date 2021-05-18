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
    public function index(Request $request = null)
    {
        $dateFormat = 'Y-m-d';
        $filters = $this->getFilters($request);
        setcookie('filters', json_encode($filters), time() + 3600 * 60 * 30);

        $where = '';
        if ($filters['systemTypes']) {
            $where .= ($where ? " AND " : "") . "type IN (" . implode(',', $filters['systemTypes']) . ")";
        }
        if ($filters['systemTh']) {
            $where .= ($where ? " AND " : "") . "(threshold >= " . $filters['systemTh'] . " OR threshold = 0)";
        }
        $systemEvents = [];
        $rows = DB::select("SELECT * FROM ovg_systems_log " . ($where ? "WHERE " . $where : "") . " ORDER BY created DESC, player_id");
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
            $where .= ($where ? " AND " : "") . "type IN (" . implode(',', $filters['playerTypes']) . ")";
        }
        $playerEvents = [];
        $ids = [];
        $alliances = [];
        $rows = DB::select("SELECT * FROM ogv_players_log " . ($where ? "WHERE " . $where : "") . " ORDER BY created DESC");
        $events = EventPlayer::hydrate($rows);
        $events->load('player');
        foreach ($rows as $row) {
            if ($row->type == 70) {
                $json = json_decode($row->json);
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
            'systemEvents' => $systemEvents,
            'playerEvents' => $playerEvents,
            'alliances' => $alliances,
            'filters' => $filters,
        ]);
    }

    private function getFilters(Request $request = null)
    {
        $filters = [
            'system' => [],
            'systemTh' => 0,
            'systemTypes' => [],
            'player' => [],
            'playerTypes' => [],
        ];

        if ($_COOKIE['filters']) {
            $filters = json_decode($_COOKIE['filters'], true);
        }

        if ($request) {
            $system = null;
            $player = null;
            if ($request->has('filterSystem')) {
                $filters['system'] = (array)$request->get('system');
                $filters['systemTh'] = intval($request->get('systemTh'));
            } elseif ($request->has('filterPlayer')) {
                $filters['player'] = (array)$request->get('player');
            }

            $filters['systemTypes'] = $this->getFilterTypes($filters['system']);
            $filters['playerTypes'] = $this->getFilterTypes($filters['player']);
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
