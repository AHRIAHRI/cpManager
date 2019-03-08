<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLogOnline extends Model
{
    //
    protected $connection = '';
    protected $table = 'logonline';

    public function __construct()
    {
        parent::__construct();
        $rbac = new Rbac();
        $this->connection = $rbac->selectProject();
    }



}
