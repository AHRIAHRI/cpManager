<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //
    public function login(CommonController $commonController){
        $credentials = request(['name', 'password']);
        $token = auth('api')->attempt($credentials);
        if (! $token) {
            return response()->json(['mesg' => '账号或者密码错误'], 200);
        }
        // return JWT for user in  Authorizathion ;
        // Get User select project
        $UserAssets = \App\Models\UserAssets::find(request()->name);
        $allProject = $UserAssets->allProject;
        $selectProject = $UserAssets->selectProject;

        // user can't select project
        $select = '';
        $selectName ='';
        $allproject = $commonController->kvAllProject();
        if($selectProject) {
           $select = $selectProject;
           $selectName = $allproject[$select];
        }else{
            if ($allProject) {
                // user Can select Project but he not;
                $allProjectInfo = json_decode($allProject, true);
                $UserAssets->selectProject = $allProjectInfo[0]['projectCode'];
                $UserAssets->save();
                $select = $allProjectInfo[0]['projectCode'];
                $selectName = $allproject[$select];
            }
        }
        return response(['status' => 'success','selectProject'=>$select , 'selectProjectName'=>$selectName])->header('Authorization' , $token);
    }
}
