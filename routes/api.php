<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OneDriveApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('customers', CustomerController::class);


Route::post('/onedrive/upload', [OneDriveApiController::class, 'handleOneDriveUpload'])->name('onedrive.upload');
Route::get('/onedrive/callback', [OneDriveApiController::class, 'handleOneDriveUpload'])->name('onedrive.callback');