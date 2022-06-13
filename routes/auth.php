<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthenticateController;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthenticateController::class, 'authenticate'])->name('login');

    Route::post('/forgot-password', [AuthenticateController::class, 'forgot']);

    Route::post('/reset-password', [AuthenticateController::class, 'reset']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthenticateController::class, 'logout']);
});
