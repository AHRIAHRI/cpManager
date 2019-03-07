<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/2/28
 * Time: 18:41
 */
namespace App\Http\Service;


use Carbon\Carbon;
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
     * @param $data
     * @return Carbon
     * 获取7天气零点的时间
     */
    public function dayAgo($data){
        return  Carbon::tomorrow()->subDays($data)->toDateTimeString();
    }

    public function formatDayTime($data1){
        $date = new Carbon($data1);
        return [$data1.' 00:00:00' ,$date->addDays(1)->toDateTimeString()];
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

