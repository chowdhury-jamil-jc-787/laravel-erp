<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OneDriveApiController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('customers', CustomerController::class);

Route::apiResource('users', UserController::class);


Route::post('/onedrive/upload', [OneDriveApiController::class, 'uploadFilesToOneDrive'])->name('onedrive.upload');
Route::get('/onedrive/callback', [OneDriveApiController::class, 'handleOneDriveUpload'])->name('onedrive.callback');


Route::get('/quotation_trackers', [OneDriveApiController::class, 'getQuotationTrackers']);

//edit quotation tracker
Route::put('/quotation_trackers/{id}', [OneDriveApiController::class, 'updateQuotationTracker']);



//search api
Route::get('/customer/search', [CustomerController::class, 'search']);
Route::get('/user/search-by-salesperson-code', [UserController::class, 'searchBySalespersonCode']);