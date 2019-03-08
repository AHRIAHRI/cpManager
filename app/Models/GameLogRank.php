<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLogRank extends Model
{
    //
    protected $connection = '';
    protected $table = 'logrank';

    public function __construct()
    {
        parent::__construct();
        $rbac = new Rbac();
        $this->connection = $rbac->selectProject();
    }



}
