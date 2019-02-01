<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Action
 * @package App\Models
 * 操作处理类 处理输入，过滤输入,平台和项目 过滤出来 到权限中判断 ，
 */
class Action
{
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
}
