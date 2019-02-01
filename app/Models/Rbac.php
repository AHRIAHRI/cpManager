<?php

namespace App\Models;

use App\Common\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * @param $permission
     * @return bool
     * 检查用户是否有单一的某个权限
     */
    public function checkUserSinglePermission($permission){
        return in_array($permission,request()->user()->userAssets->userOwnerPermission());
    }

    /**
     * @return bool
     * 检测是否为配置的master用户
     */
    public function isMaster(){
        $user = request()->user()->name;
        if(!$user){
            return false;
        }
        return in_array($user,$this->masters());
    }

    /**
     * @return array
     * 获取配置中所有的 master
     */
    public function masters(){
        $masters = explode(',',env('MASTERS'));
        if(empty($masters)){
            return  [];
        }
        $checkMaster = [] ;
        foreach ($masters as $item){
            if($item && !in_array($item,$checkMaster)){
                $checkMaster[] = $item;
            }
        }
        return $checkMaster ;
    }

    // TODO 在添加一个新的项目的时候,自动为在项目项目下创建一个管理员角色,并且自动为master用户授权管理员角色
    /*
     * -----------------------------------------------------------------------------------------
     | 遍历所有项目,如果项目没有如果admin角色,则创建一个角色并给出用户设置的权限
     | 自动为master 分配在改项目下的admin权限
     * -----------------------------------------------------------------------------------------
     */
    public function autoAuthorization(){
        $userAssets = request()->user()->userAssets;

        if(!$userAssets->selectProject){
            return false ;
        }
        // TODO 暂时粗糙点,直接插入两条数据,
        $role = new \App\Models\Role();
        $role->role = 'masterAutoAdmin';
        $role->project = $userAssets->selectProject;
        $role->nickName = '自动管理员';
        $role->actionPermissions = json_encode(['/sys/userManage']);
        if($role->save()){
           return $userAssets->updateRoleByProject($userAssets->selectProject,['masterAutoAdmin']);
        }
        return false ;
    }

    /**
     * @return mixed
     * 选着一个项目
     */
    public function selectProject(){
        $selectProject = request()->user()->userAssets->selectProject;
        $conne = config('database.connections');
        $dbs = array_keys($conne);
        if(empty($selectProject) && !in_array($selectProject,$dbs)){
            abort(403, '无效的或者不存在项目');
        }
        return trim($selectProject);
    }

    public function DB(){
        return DB::connection($this->selectProject());
    }

}
