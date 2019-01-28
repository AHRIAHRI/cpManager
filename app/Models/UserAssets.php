<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAssets extends Model
{
    //
    protected $table = 'userAssets';
    protected $primaryKey  = 'user';
    public $incrementing = false;
    public $keyType = 'string';

    /**
     * @param $key
     * @return mixed
     * 返回用户的所有归属角色
     */
    public function parseRoleByProject($key){
//        return 'parseRoleByProject';
        if($this->roles) {
            return json_decode($this->roles, true)[$key];
        }else{
            return [];
        }
    }

    /**
     * 更新指定的项目用户所有角色数据
     * @param $key project 代号
     * @param $update 更新的数据
     * @return bool
     *
     */
    public function updateRoleByProject($key,$update){
        $data = json_decode($this->roles,true);
        $data[$key] = $update ;
        $this->roles = json_encode($data);
        return $this->save();
    }

    /**
     * @return array 以kv的形式解析出用户所有的项目权限，没有则为空
     */
    public function parseAllProject(){
        $temp = [];
        if($this->allProject){
            foreach (json_decode($this->allProject,true) as $item){
                $temp[$item['projectCode']] =  $item['projectName'];
            }
        }
        return $temp ;
    }

    /**
     * @param $key
     * @return array 返回项目中含有key的 项目的具体信息，
     */
    public function  parseProject($key){
        $temp = [];
        if($this->allProject) {
            foreach (json_decode($this->allProject,true) as $item){
                if($item['projectCode'] == $key){
                    $temp = $item;
                    break;
                }
            }
        }
        return $temp ;
    }

    /**
     * @param $key
     * @param $rbac 项目权限对于的是plat字段
     * @return bool 修改状态
     */
    public function upateAllProjectRbac($key,$rbac){
        // 因为是修改权限所以必须存在这个属项目
       $data =  json_decode($this->allProject,true);
       if(empty($this->allProject) || empty($data[$key])){
           return false;
       }
       $data[$key]['plat'] = $rbac ;
       $this->allProject = json_encode($data);
       return $this->save();
    }


//    protected $connection = 'connection-name';

/*
 * CREATE TABLE ` ` (
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
