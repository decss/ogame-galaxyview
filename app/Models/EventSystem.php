<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EventSystem extends Model
{
    protected $table = 'ovg_systems_log';

    public function player()
    {
        return $this->hasOne(Player::class, 'id', 'player_id');
    }

    public function getCoordsAttribute()
    {
        return "{$this->gal}:{$this->sys}:{$this->pos}";
    }
}
