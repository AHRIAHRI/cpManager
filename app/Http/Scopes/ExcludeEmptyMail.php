<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/6
 * Time: 15:57
 */
namespace App\Http\Scopes;


use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ExcludeEmptyMail implements Scope
{
    /**
     * 应用作用域到给定的Eloquent查询构建器.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     * @translator laravelacademy.org
     */
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where([['serverid','<>',0],['roleid',"<>",''],['channel',"<>",''],['plat',"<>",'']]);
    }
}



