<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GameLogchat;
use App\Models\Action;

class logDetailController extends Controller
{
    /**
     *----------------------------------------------------------
     * 聊天日志
     * ---------------------------------------------------------
     */

    /**
     * @param Request $request
     * @param GameLogchat $chat
     * @param Action $action
     * @return array
     */
    public function chatLogs(Request $request, GameLogchat $chat, Action $action){
        $where = [] ;
        list($data,$total) = $action->handlePageData($chat,$request->currentPage,$request->pageNum,$where);
        return compact('data','total');
    }

    /**
     * @param GameLogchat $chat
     * @param Action $action
     * @return array
     * 返回聊天日志的分类选项
     */
    public function chatOptions(GameLogchat $chat, Action $action){
        return  $action->getOptionInfo($chat);
    }


}
