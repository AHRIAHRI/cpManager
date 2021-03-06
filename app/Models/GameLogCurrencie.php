<?php

namespace App\Models;

use App\Http\Scopes\ExcludeEmpty;
use Illuminate\Database\Eloquent\Model;

class GameLogCurrencie extends Model
{
    protected $connection = '';
    protected $table = 'logcurrencies';

    public function __construct()
    {
        parent::__construct();
        $rbac = new Rbac();
        $this->connection = $rbac->selectProject();
    }

    /**
     * 模型加载全局过滤
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ExcludeEmpty);
    }
    //
}




