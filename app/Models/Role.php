<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';


    public function getProjectAllRole($project){
        $temp = [] ;
        $models = $this::where('project',$project)->get() ;
        if($models) {
            foreach ($models as $roles) {
                $temp[$roles->role] = $roles->nickName;
            }
        }
        return $temp;
    }




}
