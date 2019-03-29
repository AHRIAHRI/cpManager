<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 17:36
 */
namespace App\Http\Contracts ;

/**
 * Interface Option
 * 数据来源契约
 * 考虑数据的解耦 ，后期可能试用elk做数据的来源
 * 目前使用的是mysql数据
 */

interface Data
{
    //统计页面的数据
    public function countlyUpData();
    public function countlyUpOption();

}

