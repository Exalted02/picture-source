<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisteredUserController;

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
Route::post('login', [RegisteredUserController::class, 'login'])->name('login');
Route::post('store-customer', [RegisteredUserController::class, 'store_customer'])->name('store-customer');
Route::post('store-retailer', [RegisteredUserController::class, 'store_retailer'])->name('store-retailer');
Route::post('forgot-password', [RegisteredUserController::class, 'forgotPassword'])->name('forgot-password');
Route::post('forgot-password-verify-otp', [RegisteredUserController::class, 'forget_password_verify_otp'])->name('forgot-password-verify-otp');
Route::post('reset-password', [RegisteredUserController::class, 'resetpassword'])->name('reset-password');

