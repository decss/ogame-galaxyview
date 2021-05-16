<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    protected $table = 'ogv_players';

//    public function items()
//    {
//        return $this->hasMany(SystemItem::class);
//    }

    public function alliance()
    {
        return $this->belongsTo(Alliance::class, 'ally_id');
    }

    public function getNameAttribute($value)
    {
        $cls = '';
        $flags = [];

        if ($this->a == 1) {
            $cls = !$cls ? 'color-a' : $cls;
            $flags[] = '<span class="color-a">A</span>';
        }
        if ($this->v == 1) {
            $cls = !$cls ? 'color-v' : $cls;
            $flags[] = '<span class="color-v">v</span>';
        }
        if ($this->b == 1) {
            $cls = !$cls ? 'color-b' : $cls;
            $flags[] = '<span class="color-b">b</span>';
        }
        if ($this->i === 1) {
            $cls = !$cls ? 'color-i' : $cls;
            $flags[] = '<span class="color-i">i</span>';
        }
        if ($this->i === 2) {
            $cls = !$cls ? 'color-ii' : $cls;
            $flags[] = '<span class="color-ii">I</span>';
        }
        if ($this->o == 1) {
            $cls = !$cls ? 'color-o' : $cls;
            $flags[] = '<span class="color-o">o</span>';
        }

        $status = '';
        if ($flags) {
            $status = ' ( ' . implode(' ', $flags) . ' )';
        }

        $name   = "<span class=\"{$cls}\">{$value}</span>"
                . $status;

        return $name;
    }

    public static function searchByRequest(Request $request)
    {
        $params = [];
        $where = null;
        $whereStatus = null;

        if ($request->get('name')) {
            $where .= ($where ? ' AND ' : '') . 'name LIKE "%' . $request->get('name') . '%"';
        }
        if (intval($request->get('rankMin'))) {
            $params[':rankMin'] = intval($request->get('rankMin'));
            $where .= ($where ? ' AND ' : '') . 'rank >= :rankMin';
        }
        if (intval($request->get('rankMax'))) {
            $params[':rankMax'] = intval($request->get('rankMax'));
            $where .= ($where ? ' AND ' : '') . 'rank <= :rankMax';
        }
        if ($request->get('status')) {
            foreach ($request->get('status') as $key => $val) {
                if ($key == 'ii') {
                    $key = 'i';
                    $val = 2;
                }
                $whereStatus .= ($whereStatus ? ' OR ' : '') . "{$key} = {$val}";
            }
        }

        if ($whereStatus) {
            $where .= ($where ? ' AND ' : '') . " ({$whereStatus})";
        }
        if ($where) {
            $users = DB::select("SELECT * FROM ogv_players WHERE {$where}", $params);
            return self::hydrate($users);
        }

        return null;
    }
}
