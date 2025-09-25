<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
    public function sendResponse($result, $message)
    {
    	$response = [
            'status' => 1,
            'message' => $message,
            'data'    => $result,
        ];
        return response()->json($response, 200);
    }

    public function msgResponse($message)
    {
    	$response = [
            'status' => 1,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 200)
    {
    	$response = [
            'message' => $error,
            'success' => false,
        ];
        if(!empty($errorMessages)){
            $response['error'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    public static function apiSuccess($message, $data = null) {

        $response = [
            "status" => 1,
            "message" => $message,
        ];
        if($data){
            $response['data'] = $data;
        }
        return response()->json($response);
    }

    public static function apiError($message){
        $response = [
            "status" => 0,
            "message" => $message
        ];

        return response()->json($response);
    }

    public $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

     public function __get($name)
    {
        return $this->data[$name];
    }

    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

}
