<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Scopes\ExcludeEmpty;

class GameLogShop extends Model
{
    //
    protected $connection = '';
    protected $table = 'logshop';

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

}
