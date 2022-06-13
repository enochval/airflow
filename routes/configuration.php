<?php

use App\Http\Controllers\API\V1\ConfigurationController;
use Illuminate\Support\Facades\Route;

Route::controller(ConfigurationController::class)->group(function () {
    Route::get('states', 'getStates');
    Route::get('countries', 'getCountries');
});
