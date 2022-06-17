<?php
namespace ZoodPay\Model;
class DeliveryDate extends \ZoodPay\Api\SDK\Model\Delivery
{

    private $transaction_id;

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param mixed $transaction_id
     */
    public function setTransactionId($transaction_id): void
    {
        $this->transaction_id = $transaction_id;
    }


}
