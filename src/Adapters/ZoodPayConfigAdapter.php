<?php
namespace ZoodPay\Adapters;

use ZoodPay\Api\SDK\Config;

require dirname(__DIR__, 2) . '/vendor/zoodpay/api-php-sdk/vendor/autoload.php';


class ZoodPayConfigAdapter
{
    private $config;
    /**
     * @var mixed|string[]|null
     */

    public function __construct($config)
    {
        $this->setConfig($config);
    }

    public function setConfig($config)
    {
        $cfg = new Config();
        $this->config = $config;
        $this->config["cfg"]= $cfg;

        $this->setAPICredentials();

    }
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * @return mixed
     */
    private function setAPICredentials()
    {
        $this->config["cfg"]::set('merchant_id',$this->config['merchant_key'] );
        $this->config["cfg"]::set('secret_key', $this->config['merchant_secret'] );
        $this->config["cfg"]::set('salt_key',  $this->config['merchant_salt'] );
        $this->config["cfg"]::set('api_endpoint', $this->config['merchant_api_url'] );
        $this->config["cfg"]::set('api_version',$this->config['merchant_api_ver'] );
        $this->config["cfg"]::set('market_code',  $this->config['market_code'] );
        $tmp =   $this->config["cfg"]::get('merchant_id');

    }

    public function __get($var){
        if(isset($this->config[$var])){
            return $this->config[$var];
        }
        return null;
    }

    public function __set($var, $val){
        $this->config[$var] = $val;
    }

}
