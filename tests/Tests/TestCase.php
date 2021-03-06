<?php

namespace Tests;

use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use ZoodPay\Adapters\ZoodPayConfigAdapter;
use ZoodPay\CoreZoodPay;
use ZoodPay\ZoodpayServiceProvider;
use ZoodPay\Api\SDK\Config;

class TestCase extends Orchestra
{

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \ZoodPay\ZoodpayServiceProvider::class,
        ];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'zoodpay' => ZoodpayServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

    }

    protected function setUp(): void
    {
        parent::setUp();
    }


    public function createApplication()
    {
        $app =  require dirname(__DIR__, 4).'/bootstrap/app.php';

        $app->register(ZoodpayServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }



}
