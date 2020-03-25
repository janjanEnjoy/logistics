<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 15:47
 */


namespace JanjanEnjoy\Logistics;


use Illuminate\Support\ServiceProvider;

class LogisticsFactoryProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $source = realpath(dirname(__DIR__).'/config/logistics_common.php');

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
        return [LogisticsFactory::class,'logisticsFactory'];
    }
}
