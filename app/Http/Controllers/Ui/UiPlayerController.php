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

        return view('player', [
            'player' => $player,
            'items' => $items,
        ]);
    }

    public function search(Request $request)
    {
        $players = Player::searchByRequest($request);
        if ($players) {
            $players->load('alliance');
        }
        return view('players', [
            'players' => $players
        ]);
    }
}
