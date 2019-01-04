<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/1/4
 * Time: 14:47
 */
namespace App\Http\Controllers\Api ;



use \App\Http\Controllers\Controller;


class SysContorller extends Controller {

    public function allProject(){
        $projects = config('customGame.project');
        $result = [] ;
        foreach ($projects as $project => $items){
            $result[] = ['name'=> $items['name'],'code'=>$project];
        }
        return $result;
    }
    public function changeSelect(){
        $select = request('select');

        // 处理服务器逻辑 获取username

        return 'success' ;
    }


}
