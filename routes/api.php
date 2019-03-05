<?php

use Illuminate\Http\Request;
use App\Models\Menu;

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
        Route::post('/login', 'Api\UserController@login');
    });

/**
 * 手动配置路由
 */
Route::prefix('v1')
    ->middleware('jwt.auth')
    ->group(function () {
        Route::match(['post'], '/menu', 'Api\CommonController@menu');
        Route::match(['post'], '/sys/useSet/userInfoList', 'Api\systemSetController@userInfoList');
        Route::match(['post'], '/sys/useSet/changeInfo', 'Api\systemSetController@changeInfo');
        // TODO 限制master才能访问的接口
        Route::match(['post'], '/sys/useSet/isMaster', 'Api\systemSetController@masterInfo');
        Route::match(['post'], '/sys/userProject/commitUserProject', 'Api\systemSetController@commitUserProject');
        }
    );

/**
 * 根据配置文件自动加载路由
 */
Route::prefix('v1')
    ->middleware(['jwt.auth','CheckPermission','CheckPlatChannelPermission'])
    ->group(function () {
        // 注册所有的菜单路由
        $util = new Menu();
        $util -> registerRoute();
    });
Route::get('/test','Api\CommonController@test');



