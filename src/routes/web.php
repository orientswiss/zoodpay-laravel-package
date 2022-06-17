<?php

use Illuminate\Support\Facades\Route;

Route::post('/zoodpay/callback/success', [\ZoodPay\Http\Controller\Callback::class, 'successAction']);
Route::post('/zoodpay/callback/error', [\ZoodPay\Http\Controller\Callback::class, 'errorAction']);
Route::post('/zoodpay/callback/ipn', [\ZoodPay\Http\Controller\Callback::class, 'ipnAction']);
Route::post('/zoodpay/callback/refund', [\ZoodPay\Http\Controller\Callback::class, 'refundAction']);

