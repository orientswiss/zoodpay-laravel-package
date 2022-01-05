<?php
namespace Zoodpay\Model;
class ConfigurationResponse
{


    private $min_limit;
    private $max_limit;
    private $service_name;
    private $description;
    private $service_code;
    private $instalments;


    /**
     * @return mixed
     */
    public function getMinLimit()
    {
        return $this->min_limit;
    }

    /**
     * @param mixed $min_limit
     */
    public function setMinLimit($min_limit): void
    {
        $this->min_limit = $min_limit;
    }

    /**
     * @return mixed
     */
    public function getMaxLimit()
    {
        return $this->max_limit;
    }

    /**
     * @param mixed $max_limit
     */
    public function setMaxLimit($max_limit): void
    {
        $this->max_limit = $max_limit;
    }

    /**
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * @param mixed $service_name
     */
    public function setServiceName($service_name): void
    {
        $this->service_name = $service_name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getServiceCode()
    {
        return $this->service_code;
    }

    /**
     * @param mixed $service_code
     */
    public function setServiceCode($service_code): void
    {
        $this->service_code = $service_code;
    }

    /**
     * @return mixed
     */
    public function getInstalments()
    {
        return $this->instalments;
    }

    /**
     * @param mixed $instalments
     */
    public function setInstalments($instalments): void
    {
        $this->instalments = $instalments;
    }


   public function getJsonData(){
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value,'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }
}
