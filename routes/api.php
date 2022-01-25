<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/')->group(function () {


    Route::get('ping', function () {
        return "API OK";
    });

    //login
    Route::post('login', 'AuthController@login');


    Route::group(['middleware' => 'auth:api'], function () {
        //AuthController
        Route::post('logout', 'AuthController@logout');
        
        //PreferenseController
        Route::get('get-departiments', 'PreferenseController@getDepartiments');

        //DocumentController
        Route::post('add-document', 'DocumentController@addDocumentation'); //getDocuments
        Route::get('get-documents', 'DocumentController@getDocuments'); //getDocuments
        
    });
});