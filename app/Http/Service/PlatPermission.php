<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/8
 * Time: 14:45
 */

namespace App\Http\Service;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlatPermission
 * @package App\Http\Service
 * 处理用户的输入，渠道权限控制过滤
 *
 */
class PlatPermission
{
//    public $input;
    public $inputAll;


    /**
     * PlatPermission constructor.
     * 调用这个服务必须检测有filter字段,如果没有直接抛出异常
     */
    public function __construct()
    {
        $this->inputAll = request()->all();
//        $this->input = $this->inputAll['filter'];
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
     * @return string
     * 根据用户的最大权限组装sql获取查询选择的where
     */
    public function userMaxSQLwhereTerms(){
        // 这里不可能为空,如果为空，中间件验证都没通过
        $maxPermission = request()->user()->userAssets->userPlatChannelPermission();
        if( 'all' == $maxPermission ){
            return '1 = 1';
        };
        $return = [];
        foreach ($maxPermission as $tempPlat => $tempChannel){
            if( 'all' == $tempChannel){
                $return[] = ' plat = "'. $tempPlat.'"';
            }else{
                $return[] = '(plat = "'. $tempPlat. '" and channel in ( "'.implode( '","',$tempChannel).'" ))';
            }
        }
        return implode(' or ',$return) ;
    }

    /**
     * @return string
     * 返回用户选择的where
     * 如果用户没有选择就返回用户的最大选择（时间限制为7天）
     */
    public function whereTerms(){

        if(! array_key_exists('filter',$this->inputAll) ){
            abort(403, '没有约定的操作');
        }

        $filter = $this->inputAll['filter'];
        $plat = $filter['plat'] ?? '';
        $channel = $filter['channel'] ?? '';
        $gameServerID = $filter['gameServerID'] ?? '';
        $rawGameServerID = $filter['rawGameServerID'] ?? '';
        $roleName = $filter['roleName'] ?? '';
        $rangeTime = $filter['rangeTime'] ?? '';
        $roleID = $filter['roleID'] ?? '';
        $userAccount = $filter['userAccount'] ?? '';
        $openLevel = $filter['openLevel'] ?? '';
        $rangeLevel = $filter['rangeLevel'] ?? '';

        $platChanel = $this->handlePlatChannel($plat,$channel) ;
        $roleBaseInfo = $this->handleGameInfo($gameServerID,$rawGameServerID,$roleName,$roleID,$userAccount);
        $selectRangeTime = $this->haneldTime($rangeTime);
        $selectRangeLevel = $this->handleRoleLevel($openLevel,$rangeLevel);

        $return = $platChanel.' and '.$selectRangeTime;
//        dump($roleBaseInfo);
        if($roleBaseInfo){
            $return .= ' and '.$roleBaseInfo;
        }
        if($selectRangeLevel){
            $return .= ' and '.$selectRangeLevel;
        }
        return $return;
    }

    /**
     * @param $plat
     * @param $channel
     * 处理用户的输入平台和渠道
     */
    public function handlePlatChannel($plat,$channel){
        if(empty($plat) && empty($channel)){
            // 用户如果没有输入平台和渠道 那么默认使用最大的平台和渠道权限
            return '('.$this->userMaxSQLwhereTerms().')';
        }else{
            $parseChannel = []; // 所选择的平台+渠道
            $sqlArray = []; // or
            if(!empty($channel)) {
                foreach ($channel as $item) {
                    $info = explode('/',$item);
                    $parseChannel[$info[0]][] = $info[1];
                }
                foreach ($parseChannel as $plat_1 => $channel_1){
                    $sqlArray[] = '(plat = "'. $plat_1. '" and channel in ( "'.implode( '","',$channel_1).'" ))';
                }
            }
            foreach ($plat as $item2){
                if(!in_array($item2,array_keys($parseChannel))){
                    $sqlArray[] = ' plat = "'. $item2.'"';
                }
            }
            return '('.implode(' or ',$sqlArray).')';
        }
    }

    public function haneldTime($rangeTime){
        // 时间是and操作
        if($rangeTime && is_array($rangeTime)){
            // 为时间区间选择
            $startTime = $rangeTime[0];
            $endTime = $rangeTime[1];
            $timeWhere = 'generatetime >= "'.$startTime .'" and generatetime < "'.$endTime.'"';

        }elseif( $rangeTime && is_string($rangeTime)){
            // 为多天时间选择
            $arrayWhere = [];
            foreach (explode(',',$rangeTime) as $tempDayTime){
                list($startTime,$endTime) = $this->formatDayTime($tempDayTime);
                $arrayWhere [] = '(generatetime >= "'.$startTime .'" and generatetime < "'.$endTime.'")' ;
            }
            $timeWhere =  '('.implode(' or ',$arrayWhere).')' ;
        }elseif (empty($rangeTime)){
            // 时间没有选择
            $startTime = $this->dayAgo(7);
            $timeWhere = 'generatetime >= "'.$startTime .'"' ;
        }
        return $timeWhere ;
    }

    /**
     * @param $gameServerID
     * @param $rawGameServerID
     * @param $roleName
     * @param $roleID
     * @param $userAccount
     * 处理角色的基本信息
     */
    public function handleGameInfo($gameServerID,$rawGameServerID,$roleName,$roleID,$userAccount){
        $return = [];
        $or = [];
        $and = [];
        if($gameServerID){
            $or[] = "serverid = {$gameServerID}" ;
        }
        if($rawGameServerID){
            $or[] = "rawserverid = {$rawGameServerID}" ;
        }
        if($roleName){
            $and[] = "rolename like \"%{$roleName}%\"" ;
        }
        if($roleID){
            $and[] = "roleid like \"%\_{$roleID}\_%\"" ;
        }
        if($userAccount){
            $and[] = "accountid like \"%\_{$userAccount}\"";
        }
        if($or){
            $return[] = '(' . implode(' or ',$or) . ')' ;
        }
        if($and) {
            $return[] = implode(' and ', $and) ;
        }
        return $return ? implode(' and ',$return) : '';
    }

    /**
     * @param $openLevel
     * @param $rangeLevel
     * @return string
     * 处理等级
     */
    public function handleRoleLevel($openLevel,$rangeLevel){
        if($openLevel){
            // 开启了等级过滤，
            list($minLev,$maxLev) = $rangeLevel;
            return "rolelevel >= {$minLev} and rolelevel <= {$maxLev} ";
        }
        return  '';
    }

    /**
     * @param Model $model
     * @param $currentPage
     * @param int $pageNum
     * @return array
     * 返回用户的分页数据和总数(通用的)
     */
    public function handlePageData(Model $model,$currentPage,$pageNum = 20){

        $where = $this->whereTerms();
        $newModel  = $model->whereRaw($where);
        // $newModel要先统计否则会多重return 导致一个错误的结果
        $total = $newModel->count();
        $result = $newModel
            ->orderBy('generatetime', 'desc')
            ->offset($pageNum * ($currentPage - 1))
            ->limit($pageNum)
            ->get();
//            ->toSql();
//        dump($result);exit();
        $data = [];
        foreach ($result as $items){
            $data[] = $items->toArray();
        }
        return [$data,$total] ;
    }

    /**
     * @param Model $model
     * @return array
     * 获取日志中的选项 (通用的)
     */
    public function getOptionInfo(Model $model){
        // platAndChannel rawserverids max min
        // TODO 应该懂redis中加载
        $data = [
            'platAndChannel'=>[],
            'serverIDs' => [],
            'rawServerIDs' => [],
            'lvMax' => 0,
            'lvMin' => 1000000000000000000000000000000,

        ];
        $where = $this->userMaxSQLwhereTerms();
//        $where = [];
        $newModel  =  $model->whereRaw($where);
        $result = $newModel
            ->selectRaw('plat,channel,serverid,rawserverid,MAX(rolelevel) as max ,MIN(rolelevel) as min')
            ->groupBy(['plat','channel','serverid','rawserverid'])
            ->get();
        foreach ($result as $item){
            if(!in_array($item->plat,array_keys($data['platAndChannel']))){
                $data['platAndChannel'][$item->plat] = [];
                $data['platAndChannel'][$item->plat][] = $item->channel;
            }else{
                if(!in_array($item->channel,$data['platAndChannel'][$item->plat])){
                    $data['platAndChannel'][$item->plat][] = $item->channel;
                }
            }
            if(!in_array($item->plat,array_keys($data['serverIDs']))) {
                $data['serverIDs'][$item->plat][] = $item->serverid;
            }else{
                if (!in_array($item->serverid, $data['serverIDs'][$item->plat])) {
                    $data['serverIDs'][$item->plat][] = $item->serverid;
                }
            }

            if(!in_array($item->plat,array_keys($data['rawServerIDs']))) {
                $data['rawServerIDs'][$item->plat][] = $item->rawserverid;
            }else{
                if (!in_array($item->rawserverid, $data['rawServerIDs'][$item->plat])) {
                    $data['rawServerIDs'][$item->plat][] = $item->rawserverid;
                }
            }
            if($data['lvMin'] >= $item->min){
                $data['lvMin'] = $item->min;
            }
            if($data['lvMax'] <= $item->max){
                $data['lvMax'] = $item->max;
            }
        }
        return $data;
    }




}






