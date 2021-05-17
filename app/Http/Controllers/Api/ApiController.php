<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends ApiManiController
{
    public function updateSystem(Request $request)
    {
        $postData = urldecode($request->get('data'));
        if (!$postData) {
            $this->setResp('error', 'Input data has wrong format or empty');
            return $this->getResp();
        }

        // og::channel('single')->info(
        //    "\r\n------------------------------------------------------------------------------\r\n"
        //    . urldecode($postData)
        // ;

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


}
