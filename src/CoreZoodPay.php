<?php

namespace Zoodpay;

use ConfigurationResponse;
use GuzzleHttp\Exception\BadResponseException;
use PhpParser\JsonDecoder;
use ZoodPay\MerchantApi\SDK\Model\Credit;
use ZoodPay\MerchantApi\SDK\Requests\CreateRefund;
use ZoodPay\MerchantApi\SDK\Requests\CreateTransaction;
use ZoodPay\MerchantApi\SDK\Requests\GetConfiguration;
use ZoodPay\MerchantApi\SDK\Requests\GetCreditBalance;
use ZoodPay\MerchantApi\SDK\Requests\GetRefundById;
use ZoodPay\MerchantApi\SDK\Requests\HealthCheck;
use ZoodPay\MerchantApi\SDK\Requests\SetTransactionDelivery;
use ZoodPay\MerchantApi\SDK\Requests\Signature;
use Zoodpay\Model\DeliveryDate;

require dirname(__DIR__, 1) . '/vendor/zoodpay/api-php-sdk/vendor/autoload.php';

class CoreZoodPay
{

    private $cfgAdapter;

    /**
     * @param $cfgAdapter
     */
    public function __construct($cfgAdapter)
    {
        $this->cfgAdapter = $cfgAdapter;

    }


    /**
     * @return $cfgAdapter
     */
    public function getAPISettings()
    {
        return $this->cfgAdapter;
    }

    /**
     * @return array|mixed|\PhpParser\Comment|\PhpParser\Node|string
     */
    public function getZoodPayLimit()
    {
        try {
            $market_code = $this->cfgAdapter->__get('market_code');
            $request = new GetConfiguration(['market_code' => $market_code]);
            $response = $request->get();
            $body = $response->getBody()->getContents();
            return (new JsonDecoder())->decode($body, ConfigurationResponse::class);


        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }


    }

    /**
     * @param $phone_number
     * @return mixed|string
     * @throws \JsonException
     */
    public function getCreditBalance($phone_number)
    {
        try {
            $creditModel = new Credit();
            $creditModel->setMarketCode($this->cfgAdapter->__get('market_code'));
            $creditModel->setCustomerMobile($phone_number);
            $request = new GetCreditBalance($creditModel->jsonSerialize());
            $response = $request->get();

            $body = $response->getBody()->getContents();
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }

    }

    /**
     * @param $currency
     * @param $amount
     * @param $merchant_reference_no
     * @param $transaction_id
     * @return string
     * @throws \ZoodPay\MerchantApi\SDK\Exception\InvalidArgumentException
     */
    public function getResponseSignature($amount, $merchant_reference_no, $transaction_id){
        return (new Signature())->ZoodPayResponseSignature($this->cfgAdapter->__get('market_code'),$this->cfgAdapter->__get('currency'), $amount, $merchant_reference_no, $transaction_id);
    }

    /**
     * @param $merchant_refund_reference
     * @param $refund_amount
     * @param $status
     * @param $refund_id
     * @return mixed
     */
    public function getRefundResponseSignature($merchant_refund_reference, $refund_amount, $status, $refund_id){

        return (new Signature())->ZoodPayRefundResponseSignature($merchant_refund_reference, $refund_amount, $status, $refund_id);
    }


    public function createRefund($refundModel){

        try {
            $createRefundRequest = new CreateRefund();
            $response = $createRefundRequest->create($refundModel);
            return $response->getBody()->getContents();
        }catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }

    }


    /**
     * @param $billing
     * @param $customer
     * @param $items
     * @param $order
     * @param $shipping
     * @param $shippingService
     * @return mixed
     */
    public function createTransaction($billing, $customer, $items, $order, $shipping, $shippingService){
        try {

            $signatureRequest = new Signature();
            $order = $signatureRequest->CreateTransactionSignature($order);
            $transactionRequest = new CreateTransaction();
            $response = $transactionRequest->create($billing, $customer, $items, $order, $shipping, $shippingService);
            return $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }


    /**
     * @param DeliveryDate $delivery
     * @return string|void
     */
    public function setDeliveryDate($delivery){
        try {

            $deliveryRequest = new SetTransactionDelivery($delivery->jsonSerialize());
            $response = $deliveryRequest->set($delivery->getTransactionId());

            return $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }

    }

    /**
     * checkOrderAmountVsZoodPayLimit return the Available service for checkout amount
     * @param $amount
     * @return array
     */
    public function availableZoodPayService($amount){
        $limit = $this->getZoodPayLimit();
        $tmpArray = [] ;
        if(isset($limit['configuration'])){
            foreach ($limit['configuration'] as $key => $value){
                if (($amount >= $limit['configuration'][$key]['min_limit']) && ($amount <= $limit['configuration'][$key]['max_limit'])){
                    array_push($tmpArray,$limit['configuration'][$key]);
                }
            }
        }

            return $tmpArray;

    }

    /**
     * @param $refund_id
     * @return mixed
     * @throws \JsonException
     */
    public function getRefundStatusById($refund_id){

        try {
            $request = new GetRefundById();
            $response = $request->get($refund_id);
            $body = $response->getBody()->getContents();
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }

    /**
     * @return mixed
     */
    public function apiHealthCheck(){
        try {
            $request = new HealthCheck();
            $response = $request->get();
            return $response->getBody()->getContents();

        }catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }


}
