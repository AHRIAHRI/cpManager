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

    /**
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
        // TODO 暂时粗糙点,直接插入两条数据,后续做一些优化 。。^-^
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
        $selectProject = app('general')->selectProject();
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

    /**
     *
     * -----------------------------------------------------------------------------------------
     | 检查权限 我们约定 平台统一用key [plat] 渠道统一用key [channel]
     | 约定格式为 [['plats'=>'ms1','channel'=> ['1001','213123']]，['plats'=>'ms1','channel'=> []]]
     | 前端传过来的格式  { "plat": [ "ms1", "s00", "qp1" ], "channel": [ "ms1/123", "s00/102", "s00/104" ]}
     | 如果渠道为空约定为全部渠道，否则权限继续判断
     | 如果用户有请求，则进行过滤，如果没有则放行
     * -----------------------------------------------------------------------------------------
     */
    public function checkPlatChannelPermission(){
        $input = request()->all();
        // 如果用户没传这两个key，则放行，我们约定查询数据必须携带这两个key
        if(! (array_key_exists('plat',$input) && array_key_exists('channel',$input))){
            return  ['check' => true] ;
        }else{
            $plats = $input['plat'];
            $channels = $input['channel'];
        }
        // 临时保存没有权限的渠道，
        $notPermission = [] ;
        $userCurrentPermission = request()->user()->userAssets->userPlatChannelPermission();
//        dump($userCurrentPermission);
        // 用户当前的权限
        if( 'all' == $userCurrentPermission ){
            // 用户拥有所有的渠道权限直接返回all
            return ['check' => true ];
        }
        if(empty($userCurrentPermission)){
            return ['check' => false, 'mesg'=>'没有任何权限' ];
        }
        // 用户是只是拥有单全都权限 获取它的值
        $usuerChannelPermission = [];
        foreach ($userCurrentPermission as $key1 => $items1){
            if(is_array($items1)){
                foreach ($items1 as $item1){
                    $usuerChannelPermission[] = $key1.'/'.$item1;
                }
            }
        }
        // 用户没有选择代表全部平台
        if( empty($plats) && 'all' != $userCurrentPermission ){
            return ['check' => false, 'mesg'=>'权限不够' ];
        }
        // 解析前端传过来的格式
        // 用户选择中 $all为全部渠道的平台, $parseChannel大数组对应平台和渠道
        $parseChannel = [] ;
        if(!empty($channels)) {
            foreach ($channels as $channel) {
                if(!in_array($channel,$usuerChannelPermission)){
                    $notPermission[] = $channel;
                }
                $info = explode('/',$channel);
                $parseChannel[$info[0]][] = $info[1];
            }
        }
        $all = [];
        foreach ($plats as $plat){
            if(!in_array($plat,array_keys($parseChannel))){
                $all[] = $plat ;
            }
        }
        // 如果存在查询全部渠道
        if(!empty($all)){
            foreach ($all as $item){
                if(!(isset($userCurrentPermission[$item]) && 'all' == $userCurrentPermission[$item])){
                    $notPermission[] = $item . '/all';
                }
            }
        }
        if(empty($notPermission)){
            return ['check' => true ];
        }else{
            return ['check' => false , 'mesg'=> implode(',',$notPermission).';权限不够'];
        }
    }

    /**
     * 根据用户的最大渠道平台权限,返回用户可以选择的全都和平台选项
     * (当选项为all的时候，返回具体的选择)
     */
    public function userDefaultPlatChannel(){

    }
}






