<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Action
 * @package App\Models
 * 操作处理类 处理输入，过滤输入,平台和项目 过滤出来 到权限中判断 ，
 * 这只是一个临时处理的方法，后续在完善和优化中应该使用契约对数据进行分析和管理统计实现数据的解耦
 */
class Action
{
    // 每一类日志应该有该类自身的过滤机制，这里应该是加载通用的sql过滤规则 。

    /**
     * @param Model $query
     * @param $currentPage
     * @param int $pageNum
     * @param array $where
     * @return array
     * 这里是对分页的数据做处理
     */
    public function handlePageData(Model $model,$currentPage,$pageNum = 20){

        $where = $this->fromUserInputGetSQLwhereTerms();

        $newModel  = empty($where) ? $model : $model->whereRaw($where);

        // $newModel要先统计否则会多重return 导致一个错误的结果
        $total = $newModel->count();
        $result = $newModel
            ->orderBy('generatetime', 'desc')
            ->offset($pageNum * ($currentPage - 1))
            ->limit($pageNum)
            ->get();
        $data = [];
        foreach ($result as $items){
            $data[] = $items->toArray();
        }
        return [$data,$total] ;
    }

    /**
     * @param Model $model
     * @param array $where
     * @return array
     * 获取过滤条件的Optin选项,这里使用的条件是用户最大权限选择
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
        $newModel  = empty($where) ? $model : $model->whereRaw($where);
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

    /**
     * @return mixed
     * 从登录日志中获取所有的平台和渠道
     */
    public function allPlatAndChannel(){
        // TODO 从redis中加载 定时任务中加载
        // TODO 检测用户的权限，授权人不得给别人的授权超过自己的最大权限
        $loginLog = new GameLogin();
        $result = $loginLog
            ->select('plat','channel')
            ->orderBy('generatetime', 'desc')
            ->groupBy(['plat','channel'])
            ->get();
        $temp = [] ;
        foreach ($result as $item){
           $temp[$item->plat][]= $item->channel;
        }
        krsort($temp);
        return $temp;
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
        // {"s00":["107","1008","106","s00","10086"],"qp1":"all"} 这个格式的数据进行组装成新的 where
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
    public function fromUserInputGetSQLwhereTerms(){

        $input = request()->all();
        // 我们约定查询数据必须携带filter
        if(! array_key_exists('filter',$input) ){
            return  '' ;
        }else{
            $filter = $input['filter'];
        }
        $plat = $filter['plat'] ?? '';
        $channel = $filter['channel'] ?? '';
        $gameServerID = $filter['gameServerID'] ?? '';
        $rawGameServerID = $filter['rawGameServerID'] ?? '';
        $roleName = $filter['roleName'] ?? '';
        $rangeTime = $filter['rangeTime'] ?? '2019-03-07,2019-03-04';
        $roleID = $filter['roleID'] ?? '';
        $userAccount = $filter['userAccount'] ?? '';
        $openLevel = $filter['openLevel'] ?? '';
        $rangeLevel = $filter['rangeLevel'] ?? '';


        // 时间是and操作
        if(is_array($rangeTime)){
            // 为时间区间选择
            $startTime = $rangeTime[0];
            $endTime = $rangeTime[1];
            $timeWhere = 'generatetime >= "'.$startTime .'" and generatetime < "'.$endTime.'"';

        }elseif( $rangeTime && is_string($rangeTime)){
            // 为多天时间选择
            $arrayWhere = [];
            foreach (explode(',',$rangeTime) as $tempDayTime){
                list($startTime,$endTime) = app('general')->formatDayTime($tempDayTime);
                $arrayWhere [] = '(generatetime >= "'.$startTime .'" and generatetime < "'.$endTime.'")' ;
            }
            $timeWhere =  '('.implode(' or ',$arrayWhere).')' ;
        }elseif (empty($rangeTime)){
            // 时间没有选择
            $startTime = app('general')->dayAgo(7);
            $timeWhere = 'generatetime >= "'.$startTime .'"' ;
        }

        // 没有选择任何一个项目，那么返回最大的权限 ,加上时间的过滤，不然数据量太大(7天)
//        if($plat || $channel ||  $gameServerID || $rawGameServerID || $roleName || $rangeTime || $roleID || $userAccount || $openLevel ){
            $where = $this->userMaxSQLwhereTerms() ;
            return '('.$where.') and '.$timeWhere;
//        }
//        if(empty())

        // 一个个条件做判断



    }





}
