<?php

use Aimensasi\FPX\Controllers\PaymentController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::post('payment/fpx/auth', [PaymentController::class, 'requestAuthorization'])->name('fpx.payment.auth.request');

Route::post($directPath, [PaymentController::class, 'directCallback'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [PaymentController::class, 'indirectCallback'])->name('fpx.payment.indirect.callback');
