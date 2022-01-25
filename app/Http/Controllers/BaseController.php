<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
     /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function returnResponse($message,$data)
    {
    	$response = [   
             'success' => true,  
             'message' => $message,  
             'payload'    => $data,];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     * @return \Illuminate\Http\Response
     */
    public function returnError($message, $errorsArray = [], $code = 200)
    {
    	$response = [
            'success' => false,
            'message' => $message,
            'payload' => $errorsArray,
        ];

        return response()->json($response, $code);
    }
}
