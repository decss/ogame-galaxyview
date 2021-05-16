<?php


namespace App\Http\Controllers\Ui;


use App\Models\SystemDate;
use App\Models\SystemItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use function PHPUnit\Framework\assertFileIsReadable;

class UiGalaxyController extends UiMainController
{
    private static $lastGalaxy = 6;
    private static $lastSystem = 499;
    private static $tableCols = 20;
    private static $dates = [
        (3600 * 24 * 1) => [
            'color' => 'color1',
            'text' => 'Less than 1 day',
        ],
        (3600 * 24 * 4) => [
            'color' => 'color2',
            'text' => '1 - 4 days',
        ],
        (3600 * 24 * 7) => [
            'color' => 'color3',
            'text' => '4 - 7 days',
        ],
        (3600 * 24 * 14) => [
            'color' => 'color4',
            'text' => '1 - 2 weeks',
        ],
        (3600 * 24 * 30) => [
            'color' => 'color5',
            'text' => '2 - 4 weeks',
        ],
        (3600 * 24 * 90) => [
            'color' => 'color6',
            'text' => 'More than month',
        ],
        0 => [
            'color' => 'color7',
            'text' => 'No data',
        ],
    ];

    public function index($gal = 1)
    {
        $table = self::getTable($gal);

        return view('galaxy', [
            'lastGalaxy' => self::$lastGalaxy,
            'galaxy' => $gal,
            'table' => $table
        ]);
    }

    public function view($gal, $sys)
    {
        $gal = $gal < 1 ? 1 : $gal;
        $gal = $gal > self::$lastGalaxy ? self::$lastGalaxy : $gal;
        $sys = $sys < 1 ? 1 : $sys;
        $sys = $sys > self::$lastSystem ? self::$lastSystem : $sys;

        $items = SystemItem::where(['gal' => $gal, 'sys' => $sys])->get()->keyBy('pos');
        $items->load('player', 'player.alliance');

        $date = SystemDate::where(['gal' => $gal, 'sys' => $sys])->first();

        return view('galaxy-view', [
            'galaxy' => $gal,
            'system' => $sys,
            'items' => $items,
            'date' => self::getUpdateDate($date ? $date->updated : null),
            // 'lastGalaxy' => self::$lastGalaxy,
        ]);
    }

    public function viewPost(Request $request)
    {
        $gal = $request->post('gal');
        $sys = $request->post('sys');

        return redirect()->route('galaxy.view', ['gal' => $gal, 'sys' => $sys]);
    }

    public static function getItemsTable($items)
    {
        $table = [];
        foreach ($items as $item) {
            $i = $item['pos'];
            $table[$i] = [
                'gal' => $item['gal'],
                'sys' => $item['sys'],
                'pos' => $item['pos'],
                'player_id' => $item['player_id'],
                'planet_id' => $item['planet_id'],
                'planet_name' => $item['planet_name'],
                'moon_name' => $item['moon_name'],
                'moon_size' => $item['moon_size'],
                'field_me' => $item['field_me'],
                'field_cry' => $item['field_cry'],
                'created' => $item['created'],
            ];
        }

        return $table;
    }

    public static function getTable($gal)
    {
        $dates = SystemDate::where('gal', $gal)->get();

        $s = 1;
        $rows = [];
        for ($k = 1; $k <= ceil(self::$lastSystem / self::$tableCols); $k++) {
            for ($i = 1; $i <= self::$tableCols; $i++) {
                if ($s > self::$lastSystem) {
                    continue;
                }
                $updated = null;
                foreach ($dates as $date) {
                    if ($date->gal == $gal && $date->sys == $s) {
                        $updated = $date['updated'];
                        break;
                    }
                }
                $rows[$i][$k] = [
                    's' => $s,
                    'cls' => self::getUpdateDate($updated)['color'],
                ];
                $s++;
            }
        }
        return $rows;
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
        // $day = 3600 * 24;
        // if ($diff <= $day * 1) {
        //     return 'color1';
        // } elseif ($diff <= $day * 4) {
        //     return 'color2';
        // } elseif ($diff <= $day * 7) {
        //     return 'color3';
        // } elseif ($diff <= $day * 14) {
        //     return 'color4';
        // } elseif ($diff <= $day * 30) {
        //     return 'color5';
        // } elseif ($diff <= $day * 90) {
        //     return 'color6';
        // } else {
        //     return 'color7';
        // }
    }
}
