<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 17:49
 */

namespace App\Http\Service;

use RequestFilter ;
use DB;
use General;
use App\Http\Contracts\Data ;

class SourceMysql implements Data
{


    public function countlyUpOption(){
//        list($plat,$time,$servers) = RequestFilter::countlyTerms();
        return '';
    }

    /**
     * @return array 返回用户统计表格的数据
     *
     $data = [
    'date' => '2019-01-01',
    'userLogin' => '1',
    'rechargeTotal' => '1',
    'rechargePeople' => '1',
    'loginARPU' => '1',
    'payARPU' => '1',
    'activePayRate' => '1',
    'newAddCreaetRole' => '1',
    'newAddRecharge' => '1',
    'newAddRechargePeople' => '1',
    'newAddPayARPU' => '1',
    'newAddLoginARPU' => '1',
    'newAddPayRate' => '1',
        ];
     *
     * 需要返回的字段 ，
     * date 日期
     * oleUserLogin 老用户登录
     * rechargeTotal 充值金额
     * rechargePeople 充值人数
     * loginARPU 登录ARPU
     * payARPU 付费ARPU
     * activePayRate 活跃付费率
     * newAddCreaetRole 新增创角
     * newAddRecharge 新增充值
     * newAddRechargePeople 新增充值人数
     * newAddPayARPU 新增付费ARPU
     * newAddLoginARPU 新增登录ARPU
     * newAddPayRate 新增付费率
     */
    public function countlyUpData(){
        // 那拿到用户输入的项
        list($plat,$time,$servers) = RequestFilter::countlyTerms();
        // 运行SQL 语句
        $res = General::runRawSQL('select * from logpay order by generatetime desc  limit 5;');
        dump($res);
        return  [$plat,$time,$servers] ;
    }
}





