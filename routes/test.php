<?php
use \App\Http\Controllers\TestResponseController;
use Illuminate\Support\Facades\Route;

Route::get('test/get', [TestResponseController::class, 'getResponse'])->name('response.get');
Route::post('test/post', [TestResponseController::class, 'postResponse'])->name('response.post');
