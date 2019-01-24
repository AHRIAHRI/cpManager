<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //
    public function login(){
        $credentials = request(['name', 'password']);
        $token = auth('api')->attempt($credentials);
        if (! $token) {
            return response()->json(['mesg' => '账号或者密码错误'], 200);
        }
        // return JWT for user in  Authorizathion ;
        return response(['status' => 'success'])->header('Authorization' , $token);
    }
}
