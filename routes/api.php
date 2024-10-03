<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OneDriveApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('customers', CustomerController::class);

Route::apiResource('users', UserController::class);

Route::apiResource('quotes', QuoteController::class);

Route::apiResource('signatures', SignatureController::class);


Route::post('/onedrive/upload', [OneDriveApiController::class, 'uploadFilesToOneDrive'])->name('onedrive.upload');
Route::get('/onedrive/callback', [OneDriveApiController::class, 'handleOneDriveUpload'])->name('onedrive.callback');


Route::get('/quotation_trackers', [OneDriveApiController::class, 'getQuotationTrackers']);

//edit quotation tracker
Route::put('/quotation_trackers/{id}', [OneDriveApiController::class, 'updateQuotationTracker']);



//search api
Route::get('/customer/search', [CustomerController::class, 'search']);
Route::get('/user/search-by-salesperson-code', [UserController::class, 'searchBySalespersonCode']);


//pdf generation
Route::get('generate-pdf', [PDFController::class, 'generatePDF']);


//auth routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});