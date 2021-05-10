<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends ApiManiController
{
    public function updateSystem(Request $request)
    {
        $postData = urldecode($request->get('data'));
        if (!$postData) {
            $this->setResp('error', 'Input data has wrong format or empty');
            return $this->getResp();
        }

        $array = ApiUtils::parseGalaxy($postData);
        $result = ApiUtils::updateSystem($array);

        $this->setRespStatus("success");
        $this->setRespMessage("System updated {$array['galaxy']}:{$array['system']}");
        $this->setRespData(
            json_encode($result, JSON_PRETTY_PRINT) . "\r\n"
            . json_encode($array, JSON_PRETTY_PRINT)
        );

        return $this->getResp();
    }


}
