<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiUserController;
use App\Http\Controllers\ApiCustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('apikey')->group(function () {
    Route::post('/login', [ApiCustomerController::class, 'LoginUser']);
    Route::post('/register', [ApiCustomerController::class, 'RegistrationUser']);
    Route::post('/delete', [ApiCustomerController::class, 'DeleteUser']);
    Route::post('/history', [ApiCustomerController::class, 'History']);
    Route::post('/checkout', [ApiCustomerController::class, 'Checkout']);
    Route::post('/upload-struk', [ApiCustomerController::class, 'UploadStruk']);
    Route::post('/detail-order', [ApiCustomerController::class, 'Detail']);
    Route::get('/menu', [ApiCustomerController::class, 'Menu']);
    Route::get('/merchant', [ApiCustomerController::class, 'Merchant']);
    Route::post('/status-order', [ApiCustomerController::class, 'Status']);


    // ADMIN
    Route::post('/user/login', [ApiUserController::class, 'LoginAdmin']);
    Route::post('/transaction/history', [ApiUserController::class, 'HistoryTransaction']);
    Route::post('/user/save-fcm-token', [ApiUserController::class, 'SaveFCM']);
    Route::post('/transaction/updateStatus', [ApiUserController::class, 'UpdateStatus']);
    Route::post('/user/notification', [ApiUserController::class, 'Notification']);
});
