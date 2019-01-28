<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';


    public function getProjectAllRole($project){
        $temp = [] ;
        $models = $this::where('project',$project)->get() ;
        if($models) {
            foreach ($models as $roles) {
                $temp[$roles->role] = $roles->nickName;
            }
        }
        return $temp;
    }

    /*
     *
     *
 CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` char(64) NOT NULL,
  `project` char(64) NOT NULL,
  `nickName` char(64) NOT NULL,
  `actionPermissions` text NOT NULL,

  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

role 表
    (主键) id =>
    project ( role project)=>
    role =>
    nickName =>
    action => master ,（提取出菜单栏中的，接口 ，对比角色拥有的数据库权限 ）

    create_at =>
    update_at =>

----------------------
    选出字段数据然后后update  roles key 值都是，

userAssets

	(主键) user  -> 唯一的角色名
	roles  -> plat 可以拥有多个角色 ，权限计算叠加 。{'sjjy':['admin','kefu'],''}
	selectProject ->  '选择的项目' ，
	allProject -> json[‘sjjy’，‘sjyh’，‘ ’，‘ ’，] 拥有的全部项目

    create_at -> ''
    update_at -> ''

-------------------------

user 表

----------------------------

    输入权限过滤器，使用中间件直接过滤


    */



}
