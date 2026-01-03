<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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
    Route::post('/login', [ApiController::class, 'LoginUser']);
    Route::post('/register', [ApiController::class, 'RegistrationUser']);
    Route::post('/delete', [ApiController::class, 'DeleteUser']);
    Route::post('/history', [ApiController::class, 'History']);
    Route::post('/checkout', [ApiController::class, 'Checkout']);
    Route::post('/upload-struk', [ApiController::class, 'UploadStruk']);
    Route::post('/detail-order', [ApiController::class, 'Detail']);
    Route::get('/menu', [ApiController::class, 'Menu']);
    Route::get('/merchant', [ApiController::class, 'Merchant']);
    Route::post('/status-order', [ApiController::class, 'Status']);
});
