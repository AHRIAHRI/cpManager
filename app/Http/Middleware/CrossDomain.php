<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class CrossDomain
 * @package App\Http\Middleware
 * 添加跨域访问的中间件，TODO 线上环境需要严格修改
 */
class CrossDomain
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
//        header('Access-Control-Allow-Origin:*');
//        header('Access-Control-Allow-Methods:OPTIONS POST GET ');
//        header('Access-Control-Allow-Headers:authorization,content-type');
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', '*');
        $response->header('Access-Control-Expose-Headers', 'Authorization');
        $response->header('Access-Control-Allow-Headers', 'Authorization,content-type');

//        $response->header('Access-Control-Allow-Credentials', 'true');
        return  $response;

    }
}
