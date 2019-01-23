<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class systemSetController extends Controller
{
    /**
     * 列出菜单栏中所有的项目和权限
     */
    public function showRbac(Menu $util){
        return $util->listAllPanel();
    }

    /**
     * ----------------------------------------
     *  系统设置 -> 角色管理 页面相关接口
     * ---------------------------------------
     */

    /**
     * roleList 角色列表 ，
     * userList 用户列表 ，
     * platChannelData 平台和渠道列表 ，
     */
    public function roleUserInfo(Menu $menu){
        return  ['allPermisssion' => $menu-> listAllPanel()];
    }

    /**
     * 添加一个用户
     * @return
     */
    public function userAdd(){
        $data['name'] = request() -> UserName;
        $data['email'] = request() -> tel;
        $data['password'] = request() -> passwd;
        $data['passwdCheck'] = request() -> passwdCheck;
        if($data['password']  != $data['passwdCheck'] ){
            return ['error'=>'密码不一致'] ;
        }
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

        ]);
    }

    public function userdel(){

    }
    public function roleadd(){

    }
    public function roledel(){

    }
    public function modifyOtherPasswd(){

    }
    public function modifyRolePermission(){

    }
    public function modifyUserPermission(){

    }

    /**
     * ----------------------------------------
     *  系统设置 -> 用户设置 页面相关接口
     * ---------------------------------------
     *  TODO 开发阶段返回所有服务器列表
     */
    public function userInfoList(){
        $projects = config('customGame.project');
        $result = [] ;
        foreach ($projects as $project => $items){
            $result[] = ['name'=> $items['name'],'project'=>$project];
        }
//        return 'permissionIsNotDefined' ;
        return ['projectList'=>$result];
    }

    public function changeInfo(){
        $tel = request()->tel;
        $selectProject = request()->selectProject;
        $passwd = request()->passwd;
        $user = request()->user()->name;
        return  ['abc'=>[$user,$tel,$selectProject,$passwd]];
    }


    /**
     * 项目授权 页面接口 我们约定: 当allow 为true的时候，接口拥有全部权限，否则 继续往下判断
     *
     * [['post'],'/commitUserProject','commitUserProject'],  // 提交用户项目授权
     * [['post'],'/projectUserList','projectUserList'],      // 返回用户授权列表
     * [['post'],'/platChannelList','platChannelList'],      // 返回授权项目中的平台和渠道，授权情况列表
     * [['post'],'/commitChangePlat','commitChangePlat'],      // 提交授权项目中的平台和渠道
     */

    /**
     * @return array
     * 用户的授权列表
     */
    public function platChannelList(){
        $user = request() -> user ;
//        $porjects = request() -> porjects ;

        return [
            ['project'=>'yhsy','alias'=>'圣剑纪元','plat'=> [
                ['platName'=>'s00','allow' => true,'channels'=>[['channel'=>'s01','allow'=>true],['channel'=>'s02','allow'=>false]]],
                ['platName'=>'ms1','allow' => true,'channels'=>[['channel'=>'2','allow'=>true],['channel'=>'8','allow'=>false]]],
            ]],
            ['project'=>'hxxx','alias'=>'仙侠幻想','plat'=> [
                ['platName'=>'hx1','allow' => false,'channels'=>[['channel'=>'hxx','allow'=>true]]],
            ]],
        ];
    }
}
