<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2018/12/27
 * Time: 12:00
 */
namespace App\Http\Controllers\Api ;



use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtController extends Controller
{
    public function login(){
        $credentials = request(['name', 'password']);
        $token = auth('api')->attempt($credentials);
        if (! $token) {
            return response()->json(['mesg' => '账号或者密码错误'], 200);
        }

        // return JWT for user in  Authorizathion ;
        return response(['status' => 'success'])->header('Authorization' , $token);
    }
    public function error(){
        return response()->json(['error' => 'error page'], 401);
    }

}



