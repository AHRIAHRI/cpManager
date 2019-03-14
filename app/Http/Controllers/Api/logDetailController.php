<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GameLogchat;
use App\Models\GameLogCurrencie;
use App\Models\GameLogItemDrop;
use App\Models\GameLogLevelup;
use App\Models\GameLogMail;
use App\Models\GameLogOnline;
use App\Models\GameLogPay;
use App\Models\GameLogin;
use App\Models\GameLogRank;
use App\Models\GameLogSession;
use App\Models\GameLogShop;
use App\Models\GameLogTask;
use App\Models\GameLogVirtualitem;

class logDetailController extends Controller
{
    /**
     *-----------------------------------------------------------------------------------------------------
     * 聊天日志
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * @param Request $request
     * @param GameLogchat $chat
     * @return array
     */
    public function chatLogs(Request $request, GameLogchat $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     * @param GameLogchat $chat
     * @return array
     * 返回聊天日志的分类选项
     */
    public function chatOptions(GameLogchat $Model){
        return  app('filter')->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 充值日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function rechargeLogs(Request $request, GameLogPay $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }
    public function rechargeLogsOptions(GameLogPay $Model){
        return  app('filter')->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 登录日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function loginLogs(Request $request, GameLogin $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }
    public function loginLogsOptions(GameLogin $Model){
        return  app('filter')->getOptionInfo($Model);
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 会话日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function sessionLogs(Request $request, GameLogSession $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function sessionLogsOptions(GameLogSession $Model){
        return  app('filter')->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 商城日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function shopLogs(Request $request, GameLogShop $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function shopLogsOptions(GameLogShop $Model){
        return  app('filter')->getOptionInfo($Model);
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 邮件日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function mailLogs(Request $request, GameLogMail $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function mailLogsOptions(GameLogMail $Model){
        return  app('filter')->getOptionInfo($Model);
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 升级日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function levelupLogs(Request $request, GameLogLevelup $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function levelupLogsOptions(GameLogLevelup $Model){
        return  app('filter')->getOptionInfo($Model);
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 排行榜日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function rankLogs(Request $request, GameLogRank $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function rankLogsOptions(GameLogRank $Model){
        return  app('filter')->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 任务日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function taskLogs(Request $request, GameLogTask $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function taskLogsOptions(GameLogTask $Model){
        return  app('filter')->getOptionInfo($Model);
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 掉落日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function dropLogs(Request $request, GameLogItemDrop $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function dropLogsOptions(GameLogItemDrop $Model){
        return  app('filter')->getOptionInfo($Model);
    }
    /**
     *------------------------------------------------------------------------------------------------------
     * 在线日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function onlineLogs(Request $request, GameLogOnline $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }
    public function onlineLogsOptions(GameLogOnline $Model){
        return  app('filter')->getOptionInfo($Model);
    }
    /**
     *------------------------------------------------------------------------------------------------------
     * 物品日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function virtualitemsLogs(Request $request, GameLogVirtualitem $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function virtualitemsLogsOptions(GameLogVirtualitem $Model){
        return  app('filter')->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 货币日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function currencyLogs(Request $request, GameLogCurrencie $Model){
        list($data,$total) = app('filter')->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    public function currencyLogsOptions(GameLogCurrencie $Model){
        return  app('filter')->getOptionInfo($Model);
    }

}
