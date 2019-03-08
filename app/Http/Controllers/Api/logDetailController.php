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
use App\Models\Action;

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
     * @param Action $action
     * @return array
     */
    public function chatLogs(Request $request, GameLogchat $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     * @param GameLogchat $chat
     * @param Action $action
     * @return array
     * 返回聊天日志的分类选项
     */
    public function chatOptions(GameLogchat $Model, Action $action){
        return  $action->getOptionInfo($Model);
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 充值日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function rechargeLogs(Request $request, GameLogPay $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 登录日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function loginLogs(Request $request, GameLogin $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 会话日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function sessionLogs(Request $request, GameLogSession $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 商城日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function shopLogs(Request $request, GameLogShop $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 邮件日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function mailLogs(Request $request, GameLogMail $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 升级日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function levelupLogs(Request $request, GameLogLevelup $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 排行榜日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function rankLogs(Request $request, GameLogRank $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 任务日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function taskLogs(Request $request, GameLogTask $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 掉落日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function dropLogs(Request $request, GameLogItemDrop $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 在线日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function onlineLogs(Request $request, GameLogOnline $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

    /**
     *------------------------------------------------------------------------------------------------------
     * 物品日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function virtualitemsLogs(Request $request, GameLogVirtualitem $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }


    /**
     *------------------------------------------------------------------------------------------------------
     * 货币日志
     * -----------------------------------------------------------------------------------------------------
     */

    public function currencyLogs(Request $request, GameLogCurrencie $Model, Action $action){
        list($data,$total) = $action->handlePageData($Model,$request->currentPage,$request->pageNum);
        return compact('data','total');
    }

}
