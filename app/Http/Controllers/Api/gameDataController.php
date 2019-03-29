<?php

namespace App\Http\Controllers\Api;


use RequestFilter ;
use Data;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GameLogin;


class gameDataController extends Controller
{


    /**
     *------------------------------------------------------------------------------------------------------
     * 统计数据
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * @return array
     */
    public function countUpData(){
        return Data::countlyUpData();
    }

    public function countUpDataOptions(){
        return  app('filter')->getSimpleOption(new GameLogin());
    }


}





