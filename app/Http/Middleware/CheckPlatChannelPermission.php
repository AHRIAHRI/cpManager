<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Rbac;
class CheckPlatChannelPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * 检测用户是否有平台和渠道的操作权限
     */
    public function handle($request, Closure $next)
    {
        $rbac = new Rbac();
        $result = $rbac->checkPlatChannelPermission();
//        dump($result);
        if($result['check']) {
            return $next($request);
        }else{
            return response($result['mesg'],401);
        }
    }
}
