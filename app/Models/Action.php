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
    private $where =  [['rawserverid','<>',0],['serverid','<>',0],['accountid',"<>",''],['channel',"<>",'']];

    /**
     * @param Model $query
     * @param $currentPage
     * @param int $pageNum
     * @param array $where
     * @return array
     * 这里是对分页的数据做处理
     */
    public function handlePageData(Model $model,$currentPage,$pageNum = 20,$where = []){

        if($where) {
            $where = array_merge($this->where,$where);
        }else{
            $where = $this->where;
        }

        $result = $model->where($where)
            ->orderBy('generatetime', 'desc')
            ->offset($pageNum * ($currentPage - 1))
            ->limit($pageNum)
            ->get();
        foreach ($result as $items){
            $data[] = $items->toArray();
        }
        $total = $model->where($where)->count();
        return [$data,$total] ;
    }

    /**
     * @param Model $model
     * @param array $where
     * @return array
     * 获取过滤条件的Optin选项
     */
    public function getOptionInfo(Model $model , $where = []){
        // platAndChannel rawserverids max min
        $data = [
            'platAndChannel'=>[],
            'serverIDs' => [],
            'rawServerIDs' => [],
            'lvMax' => 0,
            'lvMin' => 1000000000000000000000000000000,

        ];
        if($where) {
            $where = array_merge($this->where,$where);
        }else{
            $where = $this->where;
        }

        $result = $model->where($where)
//            ->selectRaw('plat,channel,rawserverid,MAX(rolelevel) as max ,MIN(rolelevel) as min')
            ->selectRaw('plat,channel,serverid,rawserverid,MAX(rolelevel) as max ,MIN(rolelevel) as min')
            ->groupBy(['plat','channel','serverid','rawserverid'])
//            ->groupBy(['plat','channel','rawserverid'])
            ->get();
        foreach ($result as $item){
//            dump($item->toArray());
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
//            if(!in_array($item->rawserverid,$data['rawServerIDs'][$item->plat])){
//                $data['rawServerIDs'][$item->plat] = $item->rawserverid ;
//            }
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
