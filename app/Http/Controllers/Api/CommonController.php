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
        return $result;
    }

    public function kvAllProject(){
        $projects = config('customGame.project');
        $result = [] ;
        foreach ($projects as $project => $items){
            $result[$project] = $items['name'];
        }
        return $result;
    }


    public function menu(Menu $menu){
        return  $menu->showMenu();
    }


}
