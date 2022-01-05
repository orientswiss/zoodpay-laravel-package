<?php

namespace Tests;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use JsonException;
use phpDocumentor\Reflection\Types\This;
use Zoodpay\Adapters\ZoodPayConfigAdapter;
use Tests\TestCase;
use Zoodpay\CoreZoodPay;
use Zoodpay\Model;

class CoreZoodPayTest extends TestCase
{

    /**
     * @throws JsonException
     */
    public function testGetZoodPayLimit()
    {
        $res = $this->app->zoodpay->getZoodPayLimit();
        $this->assertIsArray($res);
        $this->assertArrayHasKey("configuration", $res);

        echo "===================ZoodPay Limits==================" . "\n";
        foreach ($res["configuration"] as $key => $value) {
            $this->assertArrayHasKey("min_limit", $res["configuration"][$key]);
            $this->assertArrayHasKey("max_limit", $res["configuration"][$key]);
            $this->assertArrayHasKey("service_name", $res["configuration"][$key]);
            $this->assertArrayHasKey("description", $res["configuration"][$key]);

            printf("Service %s have Min Limit of %s, and Max Limit of %s \n", $res["configuration"][$key]["service_name"], $res["configuration"][$key]["min_limit"], $res["configuration"][$key]["max_limit"]) ;
        }
    }
    /**
     * @throws JsonException
     */
    public function testGetCreditBalance(){

        $res = $this->app->zoodpay->getCreditBalance("998");
        echo "===================ZoodPay Credit Balance==================" . "\n";
        foreach ($res["credit_balance"] as $key => $value) {

            $this->assertArrayHasKey("service_code",$res["credit_balance"][$key], "service_code not available");
            $this->assertArrayHasKey("currency",$res["credit_balance"][$key], "currency not available");
            $this->assertArrayHasKey("amount", $res["credit_balance"][$key], "amount not available");
           printf("Credit Balance of %s %s for Service %s \n",$res["credit_balance"][$key]["service_code"],$res["credit_balance"][$key]["currency"],$res["credit_balance"][$key]["amount"]) ;
        }


    }
    public function testResponseSignature(){
        echo "==========ZoodPay Signature For Callback Response==========" . "\n";
        $responseSignature = $this->app->zoodpay->getResponseSignature( "1000", "1000", "34244345355");
        echo "Signature is: ".$responseSignature;
        $this->assertCount(128, str_split($responseSignature), "Signature did Not Generate");

    }

    public function testRefundResponseSignature(){
        echo "==========ZoodPay Signature For Refund Callback Response==========" . "\n";
        $responseSignature = $this->app->zoodpay->getRefundResponseSignature( "refund-1663636","1000", "Approved", "refund-399393");
        echo "Signature is: ".$responseSignature;
        $this->assertCount(128, str_split($responseSignature), "Refund Signature did Not Generate");

    }

    public function testRefundById(){
        echo "==========ZoodPay Refund Status by Refund ID==========" . "\n";
       $refundResponse =  $this->app->zoodpay->getRefundStatusById("61aefb3a946c3");
      printf("Refund id: %s, Status %s \n",$refundResponse['refund_id'],$refundResponse['refund']['status']);
       $this->assertArrayHasKey("refund_id",$refundResponse);
       $this->assertArrayHasKey("refund",$refundResponse);

    }

    public function testApiHealthCheck(){
        echo "==========ZoodPay API Health Check==========" . "\n";
        $apiHealthCheck = $this->app->zoodpay->apiHealthCheck();
       $this->assertStringContainsString("OK",$apiHealthCheck,"API Is Not Healthy");
        echo "API Health ".$apiHealthCheck;
    }


    public function testAvailableZoodPayService(){
        echo "==========Check Available ZoodPay Service Based on the Checkout Amount==========" . "\n";
       $available =  $this->app->zoodpay->availableZoodPayService(150000);
       $this->assertArrayHasKey("service_code",$available[0], "No Service is Available");
        foreach ($available as $key => $value) {
             printf("Available Service %s\n", $available[$key]["service_code"] );
        }
    }

    public function testCreateTransaction(){


        $billing = new Model\BillingShippingInfo();
        $billing->setName("Test User");
        $billing->setPhoneNumber("998365896609");
        $billing->setAddressLine1("Test Address 1");
        $billing->setAddressLine2("Test Address 2");
        $billing->setCity("Test City");
        $billing->setCountryCode("UZ");
        $billing->setState("Test");
        $billing->setZipcode("Test-123");

        $order = new Model\OrderInfo();
        $order->setAmount(150000);
        $order->setCurrency("UZS");
        $order->setDiscountAmount(0.00);
        $order->setLang("en");
        $order->setMarketCode("UZ");
        $order->setMerchantReferenceNo("Test_" . $this->generateARandomString());
        $order->setServiceCode("ZPI");
        $order->setShippingAmount(0.00);
        $order->setTaxAmount(0.00);

        $shipping = $billing;

        $customer = new Model\CustomerInfo();
        $customer->setCustomerDob("2000-12-23");
        $customer->setCustomerEmail("test3@zoodpay.com");
        $customer->setCustomerPhone("998365896609");
        $customer->setFirstName("Test");
        $customer->setLastName("TestLast");
        //$customer->setCustomerPid(585478965);

        $shippingService = new Model\ShippingServiceInfo();
        $shippingService->setName("Test Service");
        $shippingService->setPriority("Express");
        $shippingService->setShippedAt("Date");
        $shippingService->setTracking("HHHHHHH0-hhsh");

        $items = new Model\ItemsInfo();
        $items->setName("Test Product" . $this->generateARandomString());
        $items->setCategories(["Products-Category1"]);
        $items->setCurrencyCode("UZS");
        $items->setDiscountAmount(0.00);
        $items->setPrice(150000);
        $items->setQuantity(1.00);
        $items->setSku("Test-SKU" . $this->generateARandomString());
        $items->setTaxAmount(0.00);
        echo "\n"."===================ZoodPay Create Transaction ==================" . "\n";
       $response =  $this->app->zoodpay->createTransaction($billing, $customer, $items, $order, $shipping, $shippingService);
        $this->assertStringContainsString("transaction_id", $response, "Transaction do not have transaction id");
        $this->assertStringContainsString("payment_url", $response, "Transaction Do not have Payment URL");
        $this->assertStringContainsString("expiry_time", $response, "Transaction Do not have Expiry Time");
        $this->assertStringContainsString("session_token", $response, "Transaction Do not have Session Token");
        $this->assertStringContainsString("signature", $response, "Transaction Do not have Signature");

        echo "Response: "."\n";
        printf($response);
    }

    public function testCreateRefund(){
        $refund = $this->generateARandomString();
        $refundModel = new Model\CreateRefund();
        $refundModel->setMerchantRefundReference($refund);
        $refundModel->setReason("Test Unit");
        $refundModel->setRefundAmount(800);
        $refundModel->setRequestId($refund . "-refund");
        $refundModel->setTransactionId("395317364314084");
        $body = $this->app->zoodpay->createRefund($refundModel);
        echo "===================ZoodPay Create Refund ==================" . "\n";
        $this->assertStringContainsString("refund_id", $body, "Refund do not have refund id");
        $this->assertStringContainsString("refund", $body, "refund Do not have refund details");
        echo "Response: "."\n";
        printf($body);

    }
    public function testSetDeliveryDate(){
        echo "===================ZoodPay Set Delivery Date ==================" . "\n";
        $deliveryModel = new Model\DeliveryDate();
        $deliveryModel->setDeliveredAt("2021-12-11 10:42:10");
        $deliveryModel->setFinalCaptureAmount(0);
        $deliveryModel->setTransactionId("395317364314084");
        $responseBody = $this->app->zoodpay->setDeliveryDate($deliveryModel);
        $this->assertStringContainsString("transaction_id", $responseBody, "Response do not have transaction id");
        $this->assertStringContainsString("status", $responseBody, "Response Do not have status");
        $this->assertStringContainsString("original_amount", $responseBody, "Response Do not have original_amount");
        $this->assertStringContainsString("delivered_at", $responseBody, "Response Do not have delivered_at");
        $this->assertStringContainsString("final_capture_amount", $responseBody, "Response Do not have final_capture_amount");
        echo "Response: "."\n";
        printf($responseBody);
    }

    function generateARandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }



//    public function coreZoodPayProvider()
//    {
//        $config = [
//            'merchant_key' => "",
//            'merchant_secret' => "",
//            'merchant_salt' => "",
//            'merchant_api_url' => "",
//            'merchant_api_ver' => "",
//            'market_code' => "",
//            ];
//        $configAdapter = new ZoodPayConfigAdapter($config);
//        $configAdapter->setConfig($config);
//        $coreZoodPay = new CoreZoodPay($configAdapter);
//        return [[[$coreZoodPay]]];
//    }

}
