<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLogin extends Model
{
    //
    protected $connection = '';
    protected $table = 'login';

    public function __construct()
    {
        parent::__construct();
        $rbac = new Rbac();
        $this->connection = $rbac->selectProject();
    }


}
