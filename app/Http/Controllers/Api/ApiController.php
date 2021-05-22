<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends ApiManiController
{
    public function test(Request $request)
    {
        $eventTime = strtotime('22.05.2021 02:29:40');
        $minutes = floor((time() - $eventTime) / 60);
        $time00 = ApiUtils::getActivityTime($minutes);
        $time01 = ApiUtils::getActivityTime(0, $eventTime);
        $time1 = ApiUtils::getActivityTime($minutes, strtotime(date("Y-m-d H:i:20")));
        $time2 = ApiUtils::getActivityTime($minutes, strtotime(date("Y-m-d H:i:30")));
        dd(
            date('Y-m-d H:i:s'),
            $minutes,
            $time00, $time01, $time1, $time2
        );

        $path = storage_path() . '/logs/lumen.log';
        $file = file_get_contents($path);
        if ($file) {
            $logs = explode('local.INFO:', $file);
            $data = $logs[count($logs) - 1];

            $array = ApiUtils::parseGalaxy($data);
            dd($array);
            // $result['events'] = ApiUtils::updateEvents($array);
            // $result['system'] = ApiUtils::updateSystem($array);
        }
    }

    public function updateSystem(Request $request)
    {
        $postData = urldecode($request->get('data'));
        if (!$postData) {
            $this->setResp('error', 'Input data has wrong format or empty');
            return $this->getResp();
        }

        // Log::channel('single')->info(
        //    "\r\n------------------------------------------------------------------------------\r\n"
        //    . urldecode($postData)
        // );

        $array = ApiUtils::parseGalaxy($postData);
        $result['events'] = ApiUtils::updateEvents($array);
        $result['system'] = ApiUtils::updateSystem($array);

        $this->setRespStatus("success");

        $this->setRespMessage("System updated {$array['galaxy']}:{$array['system']}");
        $this->setRespData(
            json_encode($result, JSON_PRETTY_PRINT) . "\r\n------------------\r\n"
            . json_encode($array, JSON_PRETTY_PRINT)
        );

        return $this->getResp();
    }

    public function updateMessages(Request $request)
    {
        $postData = urldecode($request->get('data'));
        if (!$postData) {
            $this->setResp('error', 'Input data has wrong format or empty');
            return $this->getResp();
        }

         Log::channel('single')->info(
            "\r\n------------------------------------------------------------------------------\r\n"
            . urldecode($postData)
         );

        $array = ApiUtils::parseMessages($postData);
        $result['espActivity'] = ApiUtils::updateEspEvents($array['esp']);

        $this->setRespStatus("success");

        $this->setRespMessage("Messages was readed");
        $this->setRespData(
            json_encode($result, JSON_PRETTY_PRINT) . "\r\n------------------\r\n"
            . json_encode($array['esp'], JSON_PRETTY_PRINT)
        );

        return $this->getResp();
    }

}
