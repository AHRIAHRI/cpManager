<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 18:06
 */

namespace App\Http\Facade;
use Illuminate\Support\Facades\Facade ;

/**
 * Class requestFilter
 * @package App\Http\Facade
 * 门面类
 */
class Data extends Facade
{
    protected static function getFacadeAccessor() {
        return 'App\Http\Contracts\Data';
    }

}



