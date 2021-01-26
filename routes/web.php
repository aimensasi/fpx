<?php

use Aimensasi\FPX\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FPX\Controller;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::post('payment/fpx/auth', [PaymentController::class, 'handle'])->name('fpx.payment.auth.request');

Route::post($directPath, [Controller::class, 'webhook'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('fpx.payment.indirect.callback');
