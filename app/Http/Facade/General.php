<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 21:03
 */


namespace App\Http\Facade;
use Illuminate\Support\Facades\Facade ;

/**
 * Class requestFilter
 * @package App\Http\Facade
 * 门面类
 */
class General extends Facade
{
    protected static function getFacadeAccessor() {
        return 'general';
    }

}



