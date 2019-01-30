<?php

namespace App\Models;

use App\Common\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * Class Rbac
 * @package App\Models
 * 权限控制模型
 */
class Rbac
{

    /**
     * @return mixed
     * 获取去掉前缀的路径
     */
    public function routerInfo(){
        $router = Route::current();
        $prefix = $router->action['prefix'];
        $path = $router->uri;
        return str_replace($prefix,'',$path);
    }

    /**
     *返回前两个菜单，菜单栏->页面的位置
     */
    public function splitPath(){
        // 对路径做一个分割
        $path = $this->routerInfo();
        $arrPath = explode('/',$path);
        // 默认是三个/分割的路径,没有理由设计的小于三个
        return  '/'.$arrPath[1].'/'.$arrPath[2];
    }

    /**
     * @param UserAssets $userAssets
     * @return bool 检查用户是否有操作权限
     */
    public function checkUserPermission(){
        return in_array($this->splitPath(),request()->user()->userAssets->userOwnerPermission());
    }

}
