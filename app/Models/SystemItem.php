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

    public function debrisField()
    {
        $debris = $this->field_me + $this->field_cry;
        return $debris ? $debris : null;
    }

    public function updatedArray()
    {
        return UiGalaxyController::getUpdateDate($this->updated);
    }
}


