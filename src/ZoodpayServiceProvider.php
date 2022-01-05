<?php

namespace Zoodpay;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Zoodpay\Adapters\ZoodPayConfigAdapter;

class ZoodpayServiceProvider extends ServiceProvider
{


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $config = $this->app['config']->get('services.zoodpay');
        $configAdapter = new ZoodPayConfigAdapter($config);
        $instance = new CoreZoodPay($configAdapter);

        $this->app->singleton('zoodpay', function() use($instance){
            return $instance;
        });
        $this->app->instance('zoodpay', $instance);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }



}
