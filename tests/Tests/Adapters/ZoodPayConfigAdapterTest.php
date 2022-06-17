<?php

namespace Tests\Adapters;
require dirname(__DIR__, 3) . '/vendor/zoodpay/api-php-sdk/vendor/autoload.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Mockery;
use Tests\TestCase;
use Tests\ZoodPayTestCase;
use Throwable;
use Zoodpay\Adapters\ZoodPayConfigAdapter;
use Zoodpay\CoreZoodPay;
use Zoodpay\ZoodpayServiceProvider;
use Zoodpay\Api\SDK\Config;


class ZoodPayConfigAdapterTest extends TestCase
{


    /**
     * @dataProvider ConfigProvider
     */
    public function testZoodPayConfigAdapter($config)
    {
        $configAdapter = new ZoodPayConfigAdapter($config);
        $configAdapter->setConfig($config);
        $cfg = $configAdapter->__get("cfg");

        $tmpConfig = $configAdapter->getConfig();
        //Checking the getConfig function if Keys are there and if cfg is instance class of Config
        $this->assertArrayHasKey("merchant_key", $tmpConfig);
        $this->assertArrayHasKey("merchant_secret", $tmpConfig);
        $this->assertArrayHasKey("merchant_salt", $tmpConfig);
        $this->assertArrayHasKey("merchant_api_url", $tmpConfig);
        $this->assertArrayHasKey("merchant_api_ver", $tmpConfig);
        $this->assertArrayHasKey("market_code", $tmpConfig);
        $this->assertArrayHasKey("cfg", $tmpConfig);
        $this->assertInstanceOf(Config::class,$tmpConfig["cfg"]);

        //Test Get single key
        $this->assertIsString($configAdapter->__get('merchant_key'));
        $this->assertIsString($configAdapter->__get('merchant_secret'));
        $this->assertIsString($configAdapter->__get('merchant_salt'));
        $this->assertIsString($configAdapter->__get('merchant_api_url'));
        $this->assertIsString($configAdapter->__get('merchant_api_ver'));
        $this->assertIsString($configAdapter->__get('market_code'));
        $this->assertInstanceOf(Config::class,$configAdapter->__get("cfg"));


        $cfg= $configAdapter->__get("cfg");
        echo "===================ZoodPay Config==================" . "\n";
        echo "Merchant Key: " . $configAdapter->__get('merchant_key') . "\n";
        echo "Merchant Secret Key: " . $configAdapter->__get('merchant_secret') . "\n";
        echo "Merchant Salt Key: " . $configAdapter->__get('merchant_salt') . "\n";
        echo "Merchant API URL: " . $configAdapter->__get('merchant_api_url') . "\n";
        echo "Merchant API Version: " . $configAdapter->__get('merchant_api_ver') . "\n";
        echo "Merchant Market Code: " . $configAdapter->__get('market_code') . "\n";


    }

    public function ConfigProvider()
    {
        return [[[
            'merchant_key' => "",
            'merchant_secret' => "",
            'merchant_salt' => "",
            'merchant_api_url' => "",
            'merchant_api_ver' => "",
            'market_code' => "",
            'currency' =>"",
            'success_url' => "",
            'error_url' => "",
            'pending_url' => ""
        ]]];
    }

}
