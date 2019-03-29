<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 15:57
 */
namespace App\Http\Facade;
use Illuminate\Support\Facades\Facade ;

/**
 * Class requestFilter
 * @package App\Http\Facade
 * 门面类
 */
class requestFilter extends Facade
{
    protected static function getFacadeAccessor() {
        return 'filter';
    }

}



