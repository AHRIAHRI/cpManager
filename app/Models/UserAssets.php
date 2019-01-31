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
    protected $fillable = ['user'];

    /**
     * @param $key
     * @return mixed
     * 返回用户的所有归属角色
     */
    public function parseRoleByProject($key){
//        return 'parseRoleByProject';
        $data = json_decode($this->roles, true);
        if($data) {
            return empty($data[$key]) ? [] : $data[$key] ;
        }else{
            return [];
        }
    }

    /**
     * @param $project
     * @return array
     * 并集多个角色项目下的最终权限
     * 1)在中间件中判断用户是否有权限对接口进行请求
     * 2)在Menu中过滤，只是显示用户拥有的权限
     */
    public function getUserPermissionByProject($key){
        $roles = $this->parseRoleByProject($key);
        if(!$roles){
            return [];
        }
        $rbac = [];
        foreach ($roles as $role){
            // 如果用户的角色模型没获取成功
            $tempRoleModel = Role::where('project',$key)->where('role',$role)->first();
            if(!$tempRoleModel){
                continue;
            }
            foreach (json_decode($tempRoleModel->actionPermissions,true) as $item){
                if(!in_array($item,$rbac)){
                    $rbac[] = $item ;
                }
            }
        }
        return  $rbac;
    }

    /**
     * 获取选着模型的的所有权限
     * request()->user()->userAssets->userOwnerPermission()
     * 获取当前用户的所有权限
     */
    public function userOwnerPermission(){
        $project = $this->selectProject;
        if($project){
            return $this->getUserPermissionByProject($project);
        }else{
            return [];
        }
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联上user模型
     */
    public function userModel(){
        return $this->hasOne('App\User','name','user');
    }

    /**
     * @param $key project 代号
     * @param $update 更新的数据
     * @return bool
     * 更新指定的项目用户所有角色数据
     *
     */
    public function updateRoleByProject($key,$update){
        $data = json_decode($this->roles,true);
        $data[$key] = $update ;
        $this->roles = json_encode($data);
        return $this->save();
    }

    /**
     * @return array
     * 以kv的形式解析出用户所有的项目权限，没有则为空
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
     * @return array
     * 返回项目中含有key的 项目的具体信息，
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

    /**
     * @param $user
     * @return mixed
     * 创建用户的时候添加一条记录
     */
    public function createRecord($user){
        return $this::create([
            'user' => $user,
            'selectProject' => ''
        ]);
    }


}
