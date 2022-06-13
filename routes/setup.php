<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\V1\SetupController;

Route::controller(SetupController::class)->group(function (){
    Route::group(['prefix' => 'payment-types'], function () {
        Route::get('', 'getPaymentTypes');
        Route::post('', 'updatePaymentTypes');
    });

    Route::post('virtual-accounts', 'setupVirtualAccount');
});
