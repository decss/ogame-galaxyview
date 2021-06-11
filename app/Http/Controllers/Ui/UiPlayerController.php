<?php


namespace App\Http\Controllers\Ui;


use App\Models\Player;
use App\Models\SystemItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UiPlayerController extends UiMainController
{
    public function player($id)
    {
        $player = Player::find($id);
        $player->load('items', 'items.api');
        $player->load('alliance');

        $sliced = 5;
        foreach ($player->items as $key => $item) {
            if (!$item->api) {
                continue;
            }

            $apis[1] = $item->api->where('type', '=', '1')->sortByDesc('date')->slice(0, $sliced);
            $apis[2] = $item->api->where('type', '=', '2')->sortByDesc('date')->slice(0, $sliced);
            $api = $apis[1]->concat($apis[2]);

            $player->items[$key]->api = $api;
        }

        // Player Activity chart data
        $activity = $this->getChartActivityData($player);

        return view('player', [
            'player' => $player,
            'activity' => $activity,
        ]);
    }

    public function search(Request $request)
    {
        $players = Player::searchByRequest($request);
        if ($players) {
            $players->load('alliance');
            $players->load('items');
        }
        return view('players', [
            'players' => $players
        ]);
    }

    private function getChartActivityData(Player $player)
    {
        $activityLabels = '';
        foreach (UiUtils::getActivityTimeline() as $time) {
            $activityLabels .= ($activityLabels ? ', ' : '') . "'{$time}'";
        }

        $activity = [];
        $sorted = $player->activity->sortBy(function ($obj, $key) {
            return Carbon::parse($obj['time']);
        });
        foreach ($sorted as $item) {
            $time = mb_substr($item->time, 0, 5);
            if (!isset($activity[$item->type][$time])) {
                $activity[$item->type][$time] = 1;
            } else {
                $activity[$item->type][$time]++;
            }
        }
        $activityData = [1 => [], 2 => [], 3 => []];
        foreach ($activity as $type => $times) {
            foreach ($times as $time => $count) {
                $activityData[$type][] = [
                    'x' => $time,
                    'y' => $count,
                ];
            }
        }

        return [
            'labels' => $activityLabels,
            'data' => $activityData,
        ];
    }
}
