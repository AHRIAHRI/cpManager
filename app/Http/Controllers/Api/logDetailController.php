<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GameLogchat;

class logDetailController extends Controller
{
    /**
     *聊天日志详细
     */
    public function chatLogs(Request $request, GameLogchat $chatlogs){
        return  $chatlogs->clauseData($request->currentPage,$request->pageNum);
    }
}
