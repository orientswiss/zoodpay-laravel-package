<?php

use Illuminate\Support\Facades\Route;

Route::post('/zoodpay/callback/success', [\Zoodpay\Http\Controller\Callback::class, 'successAction']);
Route::post('/zoodpay/callback/error', [\Zoodpay\Http\Controller\Callback::class, 'errorAction']);
Route::post('/zoodpay/callback/ipn', [\Zoodpay\Http\Controller\Callback::class, 'ipnAction']);
Route::post('/zoodpay/callback/refund', [\Zoodpay\Http\Controller\Callback::class, 'refundAction']);

