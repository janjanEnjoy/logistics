<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 15:47
 */


namespace JanjanEnjoy\Logistics;


use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class LogisticsFactoryProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * 此provider文件如果不需要作为中间件等预加载功能，则不用加入到app.php的自动部署的服务，仅用于发布配置文件，
     * php artisan vendor:public --provider=...\LogisticsFactoryProvider
     * @return void
     */
    public function boot()
    {

        $source = realpath(dirname(__DIR__) . '/config/logistics_common.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('logistics_common.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('logistics_common');
        }
        $this->mergeConfigFrom($source, 'logistics_common');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LogisticsFactory::class, function ($app) {
            return new LogisticsFactory();
        });
        $this->app->alias(LogisticsFactory::class, 'logisticsFactory');
    }

    public function provides()
    {
        return [LogisticsFactory::class, 'logisticsFactory'];
    }
}
