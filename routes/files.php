<?php
use \Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\V1\FileSystemsController;

Route::controller(FileSystemsController::class)->group(function () {
    Route::post('', 'upload');
    Route::get('', 'getFileUrl');
});
