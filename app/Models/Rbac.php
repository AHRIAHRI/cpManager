<?php

namespace App\Models;

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
     *
     */
    public function splilitPath(){
        // 对路径做一个分割
    }
}
