<?php

namespace App\Http\V1\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected function responseJson($code, $message, $data = [])
    {
        return response()->json(array(
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ), $code);
    }

}
