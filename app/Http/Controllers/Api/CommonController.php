<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/1/4
 * Time: 14:47
 */
namespace App\Http\Controllers\Api ;



use \App\Http\Controllers\Controller;
use App\Models\Menu;


/**
 * Class CommonController
 * @package App\Http\Controllers\Api
 * 公共类库，
 */
class CommonController extends Controller {

    public function allProject(){
        $projects = config('customGame.project');
        $result = [] ;
        foreach ($projects as $project => $items){
            $result[] = ['projectName'=> $items['name'],'projectCode'=>$project];
        }
//        return 'permissionIsNotDefined' ;
        return $result;
    }

    public function kvAllProject(){
        $projects = config('customGame.project');
        $result = [] ;
        foreach ($projects as $project => $items){
            $result[$project] = $items['name'];
        }
//        return 'permissionIsNotDefined' ;
        return $result;
    }
    public function changeSelect(){
        $select = request('select');

        // 处理服务器逻辑 获取username

        return 'success' ;
        return 'permissionIsNotDefined' ;
    }
    public function test(Menu $util){
//        dump($util->listAllPanel());
        return  11111111111;
    }

    public function menu(Menu $menu){
        return  $menu->showMenu();
    }


}
