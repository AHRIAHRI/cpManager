<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAssets extends Model
{
    //
    protected $table = 'userAssets';
    protected $primaryKey  = 'user';


//    protected $connection = 'connection-name';

/*
 * CREATE TABLE `userAssets` (
  `user` char(64) NOT NULL COMMENT '用户名',
  `selectProject` char(64) NOT NULL COMMENT '选择的项目',
  `allProject` varchar(1024) NOT NULL COMMENT '可以使用的所有项目',
  `roles` mediumtext NOT NULL COMMENT '所有项目的角色',
  `AdditionalPermissions` mediumtext NOT NULL COMMENT '附加的权限',
  `ReducePermissions` mediumtext NOT NULL COMMENT '减少的权限',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
}
