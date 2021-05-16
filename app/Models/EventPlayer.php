<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EventPlayer extends Model
{
    protected $table = 'ogv_players_log';

    public function player()
    {
        return $this->hasOne(Player::class, 'id', 'player_id');
    }
}
