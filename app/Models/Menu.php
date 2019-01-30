<?php

namespace App\Models;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Util
 * @package App\Models
 * 菜单列表类
 */
class Menu
{
    public function __construct()
    {
        $this->menu = config('customMenu.menu');
    }

    /**
     * 注册所有的路由
     */
    public function registerRoute(){
        foreach ($this->genetateRouterInfo() as $route){
            list($mothed,$path,$action) = $route;
            Route::match($mothed,$path,$action);
        }
    }

    /**
     * @return array
     * 从配置的菜单栏中加载路由
     */
    public function genetateRouterInfo(){
        $routes = [];
        foreach ($this->menu as $items){
            $baseController = 'Api\\'.$items['name'].'Controller';
            foreach($items['subMeun'] as $values1){
                $baseRouterPath = $values1['addr'];
                if(empty($values1['interface'])){
                    continue;
                }
                foreach($values1['interface'] as $value2){
                    list($mothed,$routerPath,$action) = $value2;
                    //  如果是测试环境允许get请求
                    if(env('APP_DEBUG')){
                        $routes[] = [['post','get'],$baseRouterPath.$routerPath,$baseController.'@'.$action];
                    }else{
                        $routes[] = [$mothed,$baseRouterPath.$routerPath,$baseController.'@'.$action];
                    }
                }
            }
        }
        return $routes;
    }

    /**
     * 拼凑出授权的格式 默认全部为 false
     *  [
     *      {menu:'充值分析',subMenu:[
     *      ['/data/game/payer','物品货币',false],
     *      ['/data/game/payer','商城分析',true],
     *      ['/data/game/money','回本分析',true],
     *      ]
     *      },
     *      {menu:'详细日志',subMenu:[['/data/log/daily','充值日志',true]]},
     * ]
     */
    public function listAllPanel(){
        $return = [];
        foreach ($this->menu as $items){
            $temp = [];
            foreach ($items['subMeun'] as  $item1){
                $temp[] = [$item1['addr'],$item1['alias'],false];
            }
            $return[] = ['menu'=>$items['alias'],'subMenu'=> $temp];
        }
        return $return;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed 用户加载的菜单栏
     */
    public function showMenu(){
        $menu = $this->menu;
        $newMenu = [] ;
        $finalPermission = request()->user()->userAssets->userOwnerPermission() ;
        // 用户如果没有权限，则返回空
        if(empty($finalPermission)){
            return [];
        }
        foreach ($menu as $key => $item){
            $temp = [];
            foreach ($menu[$key]['subMeun'] as $key2 =>$values){
                unset($menu[$key]['subMeun'][$key2]['interface']);
                if(in_array($values['addr'],$finalPermission)){
                    // 这里比较隐秘的bug，unset 在销毁字数索引的values时候,不销毁key,导致转化成对象的时候出现问题
                    // unset($menu[$key]['subMeun'][$key2])
                    $temp[] = $menu[$key]['subMeun'][$key2];
                }
            }
            $menu[$key]['subMeun'] = $temp;
        }
        // 没有选项的菜单栏不予返回显示
        foreach ($menu as $value3){
            if($value3['subMeun']){
                $newMenu [] = $value3 ;
            }
        }
        return $newMenu;
    }



}
