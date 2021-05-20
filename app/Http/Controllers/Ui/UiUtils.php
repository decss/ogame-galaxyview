<?php


namespace App\Http\Controllers\Ui;


class UiUtils
{
    public static function getActivityTimeline()
    {
        $timeline = [];

        for ($h = 0; $h <= 23; $h++) {
            for ($m = 0; $m <= 50; $m += 10) {
                $timeline[] = str_pad($h, 2, '0', STR_PAD_LEFT)
                            . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
            }
        }

        return $timeline;
    }
}
