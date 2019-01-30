<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Rbac;

class CheckActionPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rbac = new Rbac();
        if($rbac->checkUserPermission()) {
            return $next($request);
        }else{
            return response('actionPremissionIsUndefine',401);
        }
    }
}
