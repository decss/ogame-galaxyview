<?php


namespace App\Http\Controllers\Ui;


use App\Models\Player;
use App\Models\SystemItem;
use Illuminate\Http\Request;

class UiPlayerController extends UiMainController
{
    public function player($id)
    {
        $player = Player::find($id);
        $items = SystemItem::where(['player_id' => $id])->get();
        $items->load('player.alliance');


        // Player Activity chart data
        $activity = $this->getChartActivityData($player);

        return view('player', [
            'player' => $player,
            'items' => $items,
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
        foreach ($player->activity as $item) {
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
