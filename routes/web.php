<?php

use Aimensasi\FPX\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FPX\IndirectResponseController;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::post('payment/fpx/auth', [PaymentController::class, 'requestAuthorization'])->name('fpx.payment.auth.request');

Route::post($directPath, [IndirectResponseController::class, 'directCallback'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [IndirectResponseController::class, 'handle'])->name('fpx.payment.indirect.callback');
