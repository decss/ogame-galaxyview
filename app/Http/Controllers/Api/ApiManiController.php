<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiManiController extends Controller
{
//    private $request;
    private $response;

    public function __construct()
    {
    }

    protected function getResp()
    {
        return $this->response;
    }

    protected function setResp($status, $message = '', $data = null)
    {
        $this->setRespStatus($status);
        $this->setRespMessage($message);
        $this->setRespData($data);
    }

    protected function setRespStatus($status)
    {
        if ($status == 'success') {
            $this->response['status'] = 'success';
        } else {
            $this->response['status'] = 'error';
        }
    }

    protected function setRespMessage(string $message)
    {
        $this->response['message'] = $message;
    }

    protected function setRespData($data)
    {
        $this->response['data'] = $data;
    }
}
