<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2018/12/27
 * Time: 12:00
 */
namespace App\Http\Controllers\Api ;



use \App\Http\Controllers\Controller;


class TestController extends Controller
{
    public function tes1()
    {
        dump(request(['email', 'password']));
    }

    public function userset(){
        return ['abc' =>'dafag'];
    }


}



