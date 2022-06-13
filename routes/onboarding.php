<?php

use App\Http\Controllers\API\V1\OnboardController;
use Illuminate\Support\Facades\Route;


Route::post('company', [OnboardController::class, 'onboardNewCompany']);
Route::get('client', [OnboardController::class, 'getClientId']);

