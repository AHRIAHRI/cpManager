<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class AgentLogOption
 * @package App\Models
 * 选项统计表 ，定时任务去统计今天00：00之前的数据选项
 * 插入到数据库中，然后加载到reids中。如果reids没有数据，去数据库中加载
 */
class AgentLogOption extends Model
{
    //
}
