<?php

namespace ZoodPay\Http\Controller;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use ZoodPay\Adapters\ZoodPayConfigAdapter;
use ZoodPay\CoreZoodPay;

class Callback extends Controller
{
    public function successAction()
    {
        /** @var Request $request */
        $request = App::make('request');
        $requestData = $this->urlDecode( urldecode($request->getContent()));
        $request->validate($this->generalRules(),$this->messages());
       if( $request->method() === "POST" ){
           $app= App::getFacadeRoot();
           $config = $app['config']->get('services.zoodpay');
           $configAdapter = new ZoodPayConfigAdapter($config);
           $instance = new CoreZoodPay($configAdapter);
           $localSignature = $instance->getResponseSignature($requestData['amount'], $requestData['merchant_order_reference'],$requestData['transaction_id']);
           if($localSignature == $requestData['signature'] ){

               switch ($requestData['status']){
                   case "Paid" : {
                       //TODO Update Order As success

                       return \Redirect::to($config['success_url']);

                   }
                   case  "Pending" :
                   {
                    //TODO Update Order As Pending
                       return \Redirect::to($config['pending_url']);

                   }

                   }
               }
           }

        return \Redirect::to("/404");

       }


    public function errorAction(){
        /** @var Request $request */
        $request = \App::make('request');
        $requestData = $this->urlDecode( urldecode($request->getContent()));
        $request->validate($this->generalRules(),$this->messages());
        if( $request->method() === "POST" ){
            $app= App::getFacadeRoot();
            $config = $app['config']->get('services.zoodpay');
            $configAdapter = new ZoodPayConfigAdapter($config);
            $instance = new CoreZoodPay($configAdapter);
            $localSignature = $instance->getResponseSignature($requestData['amount'], $requestData['merchant_order_reference'],$requestData['transaction_id']);
            if($localSignature == $requestData['signature'] ){

                switch ($requestData['status']){
                    case "Failed" : {
                        //TODO Update Order As Failed

                        break;
                    }
                    case  "Cancelled" :
                    {
                        //TODO Update Order As Cancelled

                        break;
                    }

                }
            }
            return \Redirect::to($config['error_url']);
        }

        return \Redirect::to("/error_url");
    }

    public function ipnAction(){
        /** @var Request $request */
        $request = App::make('request');
        $requestData = $this->urlDecode( urldecode($request->getContent()));
       echo  $request->validate($this->generalRules(),$this->messages());
        $ipnResponse = "Error With Submitted Data";
        if( $request->method() === "POST" ){
            $app= App::getFacadeRoot();
            $config = $app['config']->get('services.zoodpay');
            $configAdapter = new ZoodPayConfigAdapter($config);
            $instance = new CoreZoodPay($configAdapter);
            $localSignature = $instance->getResponseSignature($requestData['amount'], $requestData['merchant_order_reference'],$requestData['transaction_id']);
            $ipnResponse = "Signature Mismatched";
            if($localSignature == $requestData['signature'] ){

                switch ($requestData['status']){
                    case "Paid" : {
                        //TODO Update Order As success

                        $ipnResponse = "Status Changed to Paid";
                        break;
                    }
                    case  "Pending" :
                    {
                        //TODO Update Order As Pending

                        $ipnResponse = "Status Changed to Pending";
                        break;
                    }
                    case "Failed" : {
                        //TODO Update Order As Failed

                        $ipnResponse = "Status Changed to Failed";
                        break;
                    }
                    case  "Cancelled" :
                    {
                        //TODO Update Order As Cancelled

                        $ipnResponse = "Status Changed to Cancelled";
                        break;
                    }

                }
            }
        }

       echo $ipnResponse;
    }
    public function refundAction(){
        /** @var Request $request */
        $request = \App::make('request');
        $requestData = $this->urlDecode( urldecode($request->getContent()));
        $refundResponse = "Error With Submitted Data";
        if( $request->method() === "POST" ) {
            $app= App::getFacadeRoot();
            $config = $app['config']->get('services.zoodpay');
            $configAdapter = new ZoodPayConfigAdapter($config);
            $instance = new CoreZoodPay($configAdapter);
            $localRefundSignature = $instance->getRefundResponseSignature($requestData['refund[merchant_refund_reference]'], $requestData['refund[refund_amount]'], $requestData['refund[status]'], $requestData['refund_id']);
            $refundResponse = "Signature Mismatched";
            if($localRefundSignature == $requestData['signature'] ) {

                switch ($requestData['refund[status]']){
                    case "Approved" : {
                        //TODO Update Refund As Approved

                        $localRefundSignature = "Status Changed to Approved";
                        break;
                    }
                    case "Declined" : {
                        //TODO Update Refund As Declined

                        $localRefundSignature = "Status Changed to Declined";
                        break;
                    }
                }



            }

        }

        echo $refundResponse;
    }

    public function refundGenralRules(){
        return [
            'refund_id' => 'required|string|max:15',
            'signature' => 'required|string|max:128',
            'refund' => [
                'merchant_refund_reference' => 'required|string|max:30',
                'refund_amount' => 'required|string|max:15',
                'status' => 'required|string|max:15',
            ]
        ];
    }

    public function generalRules()
    {
        return [
            'amount' => 'required|string|max:15',
            'created_at' => 'string|max:25',
            'errorMessage' => 'string|max:256',
            'status' => 'required|string|max:15',
            'transaction_id' => 'required|string|max:25',
            'merchant_order_reference' => 'required|string|max:25',
            'signature' => 'required|string|max:128',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.required' => 'Amount is required!',
            'status.required' => 'Status is required!',
            'merchant_order_reference.required' => 'Merchant Order Reference is required!',
            'signature.required' => 'Signature is required!',
            'transaction_id.required' => 'Transaction ID is required!'
        ];
    }

    public function urlDecode ($query) {
        $data = [];
        foreach (explode('&', $query) as $chunk) {
            $param = explode("=", $chunk);
            if ($param) {
               $data[$param[0]] = $param[1];
            }

        }
        return $data;
    }


}
