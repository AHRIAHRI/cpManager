<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAssets ;
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
     * -----------------------------------------------------------------------------------------------
     * 用户设置 页面相关接口

     * -----------------------------------------------------------------------------------------------
     *
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
     * -----------------------------------------------------------------------------------------------
     * 用户设置 页面相关接口
     *
     * [['post'],'/userInfoList','userInfoList'],      // 返回用户的信息
     * [['post','get'],'/changeInfo','changeInfo'],    // 提交用户的信息
     * -----------------------------------------------------------------------------------------------
     *
     */

    /**
     * @return array
     * 返回用户所有可以选择的项目，基础信息
     */
    public function userInfoList(){
        $result = [] ;
        $allPorject = \App\Models\UserAssets::find(request()->user()->name)->allProject;
        if($allPorject) {
            foreach (json_decode($allPorject,true) as $project) {
                $result[] = ['projectName' => $project['projectName'], 'projectCode' => $project['projectCode']];
            }
        }
        return ['projectList'=>$result];
    }

    /**
     * @return array
     * 提交修改
     * TODO 选着项目的同时 把选着写入本地
     */
    public function changeInfo(){
        $tel = request()->tel;
        $selectProject = request()->selectProject;
        $passwd = request()->passwd;
        $user = request()->user()->name;
        return  ['abc'=>[$user,$tel,$selectProject,$passwd]];
    }


    /**
     * -----------------------------------------------------------------------------------------------
     * 项目授权 页面接口 我们约定: 当allow 为true的时候，接口拥有全部权限，否则 继续往下判断
     *
     * [['post'],'/commitUserProject','commitUserProject'],  // 提交用户项目授权
     * [['post'],'/projectUserList','projectUserList'],      // 返回用户授权列表
     * [['post'],'/platChannelList','platChannelList'],      // 返回授权项目中的平台和渠道，授权情况列表
     * [['post'],'/commitChangePlat','commitChangePlat'],      // 提交授权项目中的平台和渠道
     *
     * -----------------------------------------------------------------------------------------------
     */

    /**
     * 加载界面的时候返回所有用户的授权信息
     * 格式：
     * [
     *       {user:'liaoxiaotao', projects:[
     *           {projectCode:'sjjy',projectName:'圣剑纪元',owner:false},
     *           {projectCode:'yhsy',projectName:'永恒圣域',owner:true},
     *       ]},
     *       {user:'liaoxiaotao2', projects:[
     *           {projectCode:'sjjy',projectName:'圣剑纪元',owner:false},
     *           {projectCode:'yhsy',projectName:'永恒圣域',owner:true},
     *       ]}
     *   ],
     *
     * 存在这样一条数据记录,说明是有某个项目是有权限的
     * [['projectCode'=>'sjjy','projectName'=>'圣剑纪元',plat=>[],],]
     * 检查 userAssets 用是否有这个用户的记录
     * 因为你无法知道管理员是先分配角色还是项目
     * 如果没有,全部返回空
     */
    public function projectUserList(CommonController $commonController){
        $users = [] ;

        foreach(\App\Models\User::all() as $items ){
            $users[$items->name] = $items->userAssets->allProject ;
        };
        $return = [];

        // Generate user project owner information
        $allproject = $commonController->allProject();
        foreach ($allproject as $key => $values){
            $allproject[$key]['owner'] = false ;
        }
        foreach ($users as $user => $ownerProject){
            $finalTemp = $allproject;
            // If user procejt record is't exist and owner is true
            if($ownerProject){
                $temp = [];
                $projectInfo = json_decode($ownerProject,true);
                foreach ($projectInfo as $item){
                    $temp[$item['projectCode']] = $item;
                }

                foreach ($allproject as $key1 => $project){
                        if(in_array($project['projectCode'],array_keys($temp))){
                            $finalTemp[$key1]['owner'] = true ;
                    }
                }
            }
            $return[] = ['user' => $user, 'projects' => $finalTemp];
        }
        return $return;

    }

    /**
     * @param Request $request
     * @return array
     * 提交用户授权的项目
     */
    public function commitUserProject(Request $request){
        //对接受到数据做一个处理，是真假都要和原有的数据做一个对比
        $requesData = $request->data;
        $currentAssets = UserAssets::find($request->user);
        $currentData = $currentAssets->allProject;
        // 如果数据中存在记录
        $insert = [];
        if($currentData){
            $allProjctRecoed = json_decode($currentData,true);
            $temp = [];
            foreach ($allProjctRecoed as $key => $value){
                $temp[$value['projectCode']] = $value ;
            }
            foreach ($requesData as $requesItem) {
                // 如果为真 ，则判断当前是否有这一条数据 ，如果有保持不动，否则添加一条记录
                if($requesItem['owner']) {
                    // 存在这样一条数据，因为里面涉及到平台的权限，所以要保持原样
                    if (in_array($requesItem['projectCode'],array_keys($temp))) {
                        $insert[] = $temp[$requesItem['projectCode']];
                    } else {
                        unset($requesItem['owner']);
                        $insert[] = $requesItem;
                    }
                }
            }
        // 否则直接插入为真的数据
        }else{
            foreach ($requesData as $item) {
                if($item['owner']){
                    unset($item['owner']);
                    $insert [] = $item;
                }
            }
        }
        $currentAssets->allProject = json_encode($insert);
        return ['status'=>$currentAssets->save()];
    }

    /**
     * @return array
     * 用户的授权列表
     */
    public function platChannelList(){
        $user = request() -> user ;
//        $porjects = request() -> porjects ;

        return [
            ['projectCode'=>'yhsy','projectName'=>'圣剑纪元','plat'=> [
                ['platName'=>'s00','allow' => true,'channels'=>[['channel'=>'s01','allow'=>true],['channel'=>'s02','allow'=>false]]],
                ['platName'=>'ms1','allow' => true,'channels'=>[['channel'=>'2','allow'=>true],['channel'=>'8','allow'=>false]]],
            ]],
            ['projectCode'=>'hxxx','projectName'=>'仙侠幻想','plat'=> [
                ['platName'=>'hx1','allow' => false,'channels'=>[['channel'=>'hxx','allow'=>true]]],
            ]],
        ];
    }

    public function commitChangePlat(){

    }


}
