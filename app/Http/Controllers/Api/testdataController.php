<?php

namespace App\Http\Controllers\Api;

use App\Models\Rbac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserAssets;
use App\Models\GameLogchat;

class testdataController extends Controller
{
    //
    public function allPermission(GameLogchat $chat){

//        $log = $rbac->DB()->select('select * from login where roleid = "ms1_105908299_101" and rolelevel = 370');
//        $log = $rbac->DB()->table('login')->where([['roleid','ms1_105907324_101']])->paginate(15);
        $log = $chat->clauseData(2,10);
        dump($log);

//        $count  = GameLogchat::where('rolelevel',370)->count();
//        dump($count);
    }
}
