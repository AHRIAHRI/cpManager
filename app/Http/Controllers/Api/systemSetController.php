<?php

namespace App\Http\Controllers\Api;


use App\Models\Menu;
use App\Models\UserAssets ;
use App\Models\Rbac ;
use App\Models\Role ;
use App\Models\GameLogin ;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Nexmo\Client\Exception\Exception;

class systemSetController extends Controller
{
    /**
     * 列出菜单栏中所有的项目和权限
     */
    public function showRbac(Menu $Menu){
        return $Menu->listAllPanel();
    }


    /**
     * -----------------------------------------------------------------------------------------------
     * 角色管理 页面相关接口
     * TODO 用户的每一次请求都需要被记录下来
     * -----------------------------------------------------------------------------------------------
     *
     */

    /**
     * 针对单一的项目组 ，也就用户所选着的项目
     * roleList 角色列表 ，其中包含的信息 [角色名 roleName，中文名roleAlias，查看和修改权限列表(列表)modifyAcl]
     * userList 用户列表 ，其中包含的信息 [账号，电话，角色分配归属(列表)，项目权限(列表)]
     *
     */
    public function roleUserInfo(Menu $menu){
        // 管理员自己选着的项目;
        $selectProject = app('general')->selectProject();

        // 处理角色返回
        $roleInfo = [];
        foreach (Role::where('project',$selectProject)->get() as $signRole){
            $modifyAcl = $menu->listAllPanel();
            // 数据库中角色的权限,arr格式,如果有权限，则存在数据库中，不存在则代表没有权限
            $actionPermissions = $signRole->actionPermissions;
            if($actionPermissions){
                // 计算出权限
                $ownerPermission = json_decode($actionPermissions,true);
                foreach ($modifyAcl as $key1 => $items1){
                    foreach ($items1['subMenu'] as $key2 => $item2 ){
                        if(in_array($item2[0],$ownerPermission)){
                            $modifyAcl[$key1]['subMenu'][$key2][2] = true ;
                        }
                    }
                }
            }
            $roleInfo[] = [
                'roleName' => $signRole->role,
                'roleAlias' => $signRole->nickName,
                'modifyAcl' => $modifyAcl

            ];
        };

        // 处理用户列表返回
        $userInfo = [] ;
        // 拥有该项目的用户userAssets模型
        $userAssetsModels = app('general')->ownerProjectUsers();
        $roles = new Role();
        $projectRoles = $roles->getProjectAllRole($selectProject);

        foreach ($userAssetsModels as $assets){
            $finalRoleFormate = []; //最终计算出来的授权格式;
            $userRole = $assets->parseRoleByProject($selectProject);
            if ($projectRoles) {
                foreach ($projectRoles as $role => $nickName) {
                    if (in_array($role, $userRole)) {
                        $finalRoleFormate[] = [
                            'role' => $role,
                            'status' => true,
                            'roleName' => $nickName
                        ];
                    } else {
                        $finalRoleFormate[] = [
                            'role' => $role,
                            'status' => false,
                            'roleName' => $nickName
                        ];
                    }
                }
            }
            $userInfo[] = [
                'userName' => $assets->user,
                'tel' => $assets->userModel->email,
                'platAndChannel' => '',
                'userRole' => $finalRoleFormate
            ];
        }

        return ['roleList' => $roleInfo ,'userList' => $userInfo];
    }

    /**
     * 添加一个用户
     * @return
     * TODO 不相信所有外来数据，对所有数据做过滤
     */
    public function userAdd(UserAssets $UserAssets){
        $data['name'] = request()->UserName;
        $data['email'] = request()->tel;
        $data['password'] = request()->passwd;
        $data['passwdCheck'] = request()->passwdCheck;
        if($data['password']  != $data['passwdCheck'] ){
            return ['error' => '密码不一致'] ;
        }
        // TODO 同样也在userAssets里面添加一条记录
        return $UserAssets->createRecord($data['name']) ? User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

        ]) : false;
    }

    public function userDel(){

    }

    /**
     * 处理添加角色逻辑
     */
    public function roleAdd(Request $request){

        $project = app('general')->selectProject();
        if(!$project){
            return  [
                'status' => false,
                'mesg' => '请先选着一个项目再操作'
            ];
        }
        $roleName = $request->roleName;
        $nickName = $request->alias;

        // TODO 检查是否合法

        $role = new Role();
        $role->role = $roleName;
        $role->project = $project;
        $role->nickName = $nickName;
        $role->actionPermissions = '';
        return [
            'status' => $role->save(),
            'mesg' => ''
        ];

    }
    public function roledel(){

    }
    public function modifyOtherPasswd(Request $request){

    }

    /**
     * @param Request $request
     * @return mixed
     * TODO 检测输入是否合法
     */
    public function modifyRolePermission(Request $request){
        $project = app('general')->selectProject();
        $role = $request->role;
        $newAcl = $request->newAcl;

        $ownerAcl = [];
        foreach ($newAcl as $value){
            foreach ($value['subMenu'] as $item){
                if($item[2]){
                    $ownerAcl[] = $item[0];
                }
            }
        }
        $status = true;
        if($ownerAcl) {
            $status = \App\Models\Role::where('role', $role)->where('project', $project)->update(['actionPermissions'=>json_encode($ownerAcl)]);
        }

        return ['status'=>$status];

    }

    public function modifyUserOwnerRoles(Request $request){
        // TODO 检验输入是否合格
        $select = $request->select;
        $user = $request->user;
        $project = app('general')->selectProject();
        $insert = [];
        foreach ($select as $item){
            if($item['status']){
                $insert[] = $item['role'];
            }
        }
        return ['status' => UserAssets::find($user)->updateRoleByProject($project,$insert)];


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
        $allPorject = UserAssets::find(request()->user()->name)->allProject;
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
     * TODO 选着项目的同时 把选着写入本地localStorage
     */
    public function changeInfo(Request $request){
        $tel = $request->tel;
        $selectProject = $request->selectProject;
        $passwd = $request->passwd;
        $userModel = $request->user();

        // 修改密码 TODO 判断密码的长度
        $passwdStatus = '';
        if($passwd) {
            $userModel->password = Hash::make($passwd);
            $passwdStatus = $userModel->save();
        }
        // 修改手机号 TODO 判断手机号码是否规范
        $telStatus ='';
        if($tel){
            $userModel->email = $tel;
            $telStatus = $userModel->save();
        }
        // 修改选着的项目
        $selectProjectStatus = '';
        if($selectProject){
            $userAssets = UserAssets::find($userModel->name);
            $allProject = $userAssets->allProject;
            $temp = [];
            foreach (json_decode($allProject,true) as $items){
                $temp [] = $items['projectCode'];
            }
            if(in_array($selectProject,$temp)) {
                $userAssets->selectProject = $selectProject;
                $selectProjectStatus = $userAssets->save();
            }
        }
        return  compact('passwdStatus','telStatus','selectProjectStatus');
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
    public function masterInfo(CommonController $commonController ,Rbac $rbac){
        if(!$rbac->isMaster()){
            return  ['isMaster'=>false,'masterData'=>[]];
        }
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
        return ['isMaster'=>true,'masterData'=>$return];

    }

    /**
     * @param Request $request
     * @return array
     * 提交用户授权的项目
     */
    public function commitUserProject(Request $request,Rbac $rbac){

        // 如果不是master用户 拒绝处理 ，
        if(!$rbac->isMaster()){
            return  [ 'status' => false ];
        }

        //对接受到数据做一个处理，是真假都要和原有的数据做一个对比
        $requesData = $request->data;
        $currentAssets = UserAssets::find($request->user);
        $currentData = $currentAssets->allProject;
        // 如果数据中存在记录
        $insert = [];
        $finalOwner = [] ;  // 所有已经选的项目
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
                    $finalOwner[] = $requesItem['projectCode'] ;
                }
            }
        // 否则直接插入为真的数据
        }else{
            foreach ($requesData as $requesItem) {
                if($requesItem['owner']){
                    unset($requesItem['owner']);
                    $insert [] = $requesItem;
                    $finalOwner[] = $requesItem['projectCode'] ;
                }
            }
        }
        $currentAssets->allProject = json_encode($insert);
        // 如果有选择了,并且这个项目不在其中,重置了他的选着
        if($currentAssets->selectProject && !in_array($currentAssets->selectProject,$finalOwner)){
            $currentAssets->selectProject = '';
        }
        return [
            'status' => $currentAssets->save()
        ];
    }

    /**
     * 系统设置 -> 渠道授权
     * -----------------------------------------------------------------------------------------------
     * @return array
     * 用户的授权列表
     * 数据库格式如下：
     * plat = 'all' 全部权限
     * plat = ['s00' => 'all', ms1=>['01,123,123,1231'] ] 一部分权限
     * 组装前端数据如下,
     *
     * ['all' => false, 'plats' => [
     *     ['platName'=>'s00','allow' => true,'channels'=>[['channel'=>'s01','allow'=>true],['channel'=>'s02','allow'=>false]]],
     *     ['platName'=>'ms1','allow' => true,'channels'=>[['channel'=>'2','allow'=>true],['channel'=>'8','allow'=>false]]],
     *  ]
     * ]
     */
    public function allPlat(){
        // 获取所有的平台和渠道
        $action = new GameLogin();
        $platChannels = $action->allPlatAndChannel();

        // 组装成前端约定的权限格式
        $permissionFormate_1 = [ 'all'=>false, 'plats'=>[],];
        foreach ($platChannels as $plat => $channels){
            $tempChannels = [];
            foreach ($channels as $channel){
                $tempChannels[] = ['channel' => $channel,'allow' => false];
            }
            $permissionFormate_1['plats'][] = ['platName' => $plat ,'allow' => false, 'channels' => $tempChannels];
        }

        // 最终的格式
        $userAssetsModels = app('general')->ownerProjectUsers();
        $userPlatChanenlInfos = [] ;
        foreach ($userAssetsModels as $assets){

            // 下一个用户需要重置所有选项，否则在上一个用户的信息中遍历，产生异常的结果
            $permissionFormate = $permissionFormate_1;

            $userPlatChanenlInfo['user'] = $assets->user;
            $userOwnerPermission = $assets->userPlatChannelPermission();

            if('all' == $userOwnerPermission){
                // 拥有所有的项目权限的情况
                $permissionFormate['all'] = true ;
                $userPlatChanenlInfo['platChannel'] =  $permissionFormate;
            }else{

                if($userOwnerPermission){
                    // 拥有一部分权限的情况
                    foreach ($userOwnerPermission as $key1 => $values1){
                        if('all' == $values1){
                            foreach ($permissionFormate['plats'] as $key2 => $values2){
                                if($values2['platName'] == $key1){
                                    $permissionFormate['plats'][$key2]['allow'] = true ;
                                }
                            }
                        }elseif(is_array($values1)){
                            foreach ($values1 as $item3){
                                foreach ($permissionFormate['plats'] as $key2 => $values2){
                                    if($values2['platName'] == $key1){
                                        break;
                                    }
                                }
                                foreach ($permissionFormate['plats'][$key2]['channels'] as $key4 => $items){
                                    if($item3 == $items['channel']){
                                        $permissionFormate['plats'][$key2]['channels'][$key4]['allow'] = true;
                                    }
                                }
                            }
                        }
                    }
                }
                // 没有任何权限的情况
                $userPlatChanenlInfo['platChannel'] =  $permissionFormate;
            }
            $userPlatChanenlInfos [] = $userPlatChanenlInfo ;
        }
        return $userPlatChanenlInfos;
    }

    /**
     * 处理提交的授权
     */
    public function commitAuthorization(Request $request){
        $user = $request->user;
        $data = $request->data[0];
        if($data['all']){
            // 拥有全部权限，不再继续判断
            $return = 'all';
        }else{
            // 继续判断平台权限
            $return = [];
            foreach ($data['plats'] as $key => $plats){
                if($plats['allow']){
                    // 拥有全部平台的权限
                    $return[$plats['platName']] = 'all';
                }else{
                    // 继续判断渠道权限
                    foreach ($plats['channels'] as $channel){
                        if($channel['allow']) {
                            $return[$plats['platName']][] = $channel['channel'];
                        }
                    }
                }
            }
        }
        return ['status' => UserAssets::find($user)->updateUserPlatChannelPermission($return) ];
    }


}









