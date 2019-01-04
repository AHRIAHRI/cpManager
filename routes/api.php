<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 检查是否登录

Route::prefix('v1')
    ->group(function (){
//        Route::options('/{all?}/{abc?}', function () {
////            header("Access-Control-Allow-Origin: *");
//            header("Access-Control-Allow-CrOriginedentials: true");
//            header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
//            header('Access-Control-Allow-Headers: authorization,content-type');
//        })->where(['all' => '([a-zA-Z0-9-]|/)+']);

        Route::post('/login', 'Api\JwtController@login');
    });

Route::prefix('v1')
    ->middleware('jwt.auth')
    ->group(function () {
        Route::post('/test/set', 'Api\TestController@userset');
        Route::post('/test/test1', 'Api\TestController@test1');



        Route::post('/sys/allproject', 'Api\SysContorller@allProject');
        Route::post('/sys/change', 'Api\SysContorller@changeSelect');
    });

Route::get('/sys/allproject', 'Api\SysContorller@allProject');
Route::get('/sys/change', 'Api\SysContorller@changeSelect');



Route::any('/error', 'Api\JwtController@error');

