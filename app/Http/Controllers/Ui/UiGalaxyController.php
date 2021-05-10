<?php


namespace App\Http\Controllers\Ui;


use App\Models\SystemDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use function PHPUnit\Framework\assertFileIsReadable;

class UiGalaxyController extends UiMainController
{
    private static $lastGalaxy = 6;
    private static $lastSystem = 499;
    private static $tableCols = 20;

    public function index(Request $request)
    {
        $gal = $request->get('g', 1);
        $table = self::getTable($gal);

        return view('galaxy', [
            'lastGalaxy' => self::$lastGalaxy,
            'galaxy' => $gal,
            'table' => $table
        ]);
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
                    'cls' => self::getDateColor($updated),
                ];
                $s++;
            }
        }
        return $rows;
    }

    private static function getDateColor($date)
    {
        $diff = time() - strtotime($date);
        $day = 3600 * 24;
        if ($diff <= $day * 1) {
            return 'color1';
        } elseif ($diff <= $day * 4) {
            return 'color2';
        } elseif ($diff <= $day * 7) {
            return 'color3';
        } elseif ($diff <= $day * 14) {
            return 'color4';
        } elseif ($diff <= $day * 30) {
            return 'color5';
        } elseif ($diff <= $day * 90) {
            return 'color6';
        } else {
            return 'color7';
        }
    }
}
