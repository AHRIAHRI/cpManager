<?php

namespace App\Models;

use App\Http\Scopes\ExcludeEmpty;
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
    /**
     * 模型加载全局过滤
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ExcludeEmpty);
    }


    /**
     * @return mixed
     * 从登录日志中获取所有的平台和渠道
     */

    public function allPlatAndChannel(){
        // TODO 从redis中加载 定时任务中加载
        // TODO 检测用户的权限，授权人不得给别人的授权超过自己的最大权限
//        $loginLog = new GameLogin();
        $result = $this->select('plat','channel')
            ->orderBy('generatetime', 'desc')
            ->groupBy(['plat','channel'])
            ->get();
        $temp = [] ;
        foreach ($result as $item){
            $temp[$item->plat][]= $item->channel;
        }
        krsort($temp);
        return $temp;
    }




}
