<?php


namespace App\Http\Controllers\Ui;


use App\Models\Alliance;
use App\Models\EventPlayer;
use App\Models\EventSystem;
use Illuminate\Support\Facades\DB;

class UiEventsController extends UiMainController
{
    public function index()
    {
        $dateFormat = 'Y-m-d H:i';
        $dateFormat = 'Y-m-d';

        // $events = EventSystem::where('id', '>', '0')->get();
        $events = [];
        $systemEvents = [];
        $rows = DB::select("SELECT * FROM ovg_systems_log ORDER BY created DESC, player_id");
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


        $events = [];
        $playerEvents = [];
        $ids = [];
        $alliances = [];
        $rows = DB::select("SELECT * FROM ogv_players_log ORDER BY created DESC");
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
        ]);
    }
}
