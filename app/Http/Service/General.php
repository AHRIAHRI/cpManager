<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/2/28
 * Time: 18:41
 */
namespace App\Http\Service;



class General
{
    /**
     * @return mixed
     * 获取用户选择的项目
     */
    public function selectProject(){
        return request()->user()->userAssets->selectProject;
    }

    /**
     * @return object
     * 返回拥有user选择的项目的所有users
     */
    public function ownerProjectUsers(){
        foreach (\App\Models\UserAssets::all() as $userAssets){
            $tempUserOwner = [];
            foreach (json_decode($userAssets->allProject,true) as $userOwnerPorject){
                $tempUserOwner[] = $userOwnerPorject['projectCode'];
            };
            if(in_array($this->selectProject(),$tempUserOwner)){
                $userAssetsModels[] = $userAssets ;
            }
        }
        return $userAssetsModels ;
    }

}

