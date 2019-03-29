<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //游戏中常用的服务,用户的选择等等
        $this->app->singleton('general',function(){
            return new \App\Http\Service\General();
        });
        // 处理玩家的过滤
        $this->app->singleton('filter',function(){
            return new \App\Http\Service\PlatPermission();
        });
        // TODO redis + 调度 预先计算服务


        // 目前是由MySQL提供数据服务，后续应该使用 Elasticsearch ;
        $this->app->bind('App\Http\Contracts\Data','App\Http\Service\SourceMysql');
//        $this->app->bind('App\Http\Contracts\Data','App\Http\Service\SourceElasticsearch');
    }
}
