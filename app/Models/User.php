<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table = 'users';

    public function userAssets(){
        return $this->hasOne('App\Models\UserAssets','user','name');
    }

}
