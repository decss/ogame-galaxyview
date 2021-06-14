<?php


namespace App\Models;


use App\Http\Controllers\Ui\UiGalaxyController;
use Illuminate\Database\Eloquent\Model;

class SystemItem extends Model
{
    protected $table = 'ovg_systems';

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    // public function api()
    // {
    //     return $this->hasMany(SystemApi::class, 'system_id')
    //          ->orderBy('coords')
    //          ->orderBy('type')
    //          ->orderBy('date', 'desc')
    //         ;
    // }

    public function debrisField()
    {
        $debris = $this->field_me + $this->field_cry;
        return $debris ? $debris : null;
    }

    public function updatedArray()
    {
        return UiGalaxyController::getUpdateDate($this->updated);
    }

    public function getCoordsAttribute()
    {
        $coords = "{$this->gal}:{$this->sys}:{$this->pos}";
        return $coords;
    }

    public static function getUpdateDate($date)
    {
        $diff = time() - strtotime($date);
        foreach (self::$dates as $time => $array) {
            if ($diff <= $time) {
                return $array;
            }
        }

        return self::$dates[0];
    }

    public function getUpdateColor()
    {
        $date = UiGalaxyController::getUpdateDate($this->updated);
        return $date['color'];
    }
}


