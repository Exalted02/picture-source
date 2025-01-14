<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,CategoryController,SearchController,ForgotPasswordController,PopularController,NotificationController,ChangePassword,AnalyticController};
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/google-callback', [AuthController::class, 'googleLogin']);

//Forget Password
Route::post('send-link-forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']); 

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');

Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::group(['middleware' => ['user']], function() {
    Route::get('categories',[CategoryController::class, 'getCategories']);
    Route::post('filter-by-category',[CategoryController::class, 'filterByCategory']);
    Route::post('get-nearby-store', [SearchController::class, 'getNearByStore']);
    Route::get('recent-search-store', [SearchController::class, 'recentSearch']);
    Route::get('get-popular-store', [PopularController::class, 'getPopularStore']);
    Route::get('get-banner-store', [PopularController::class, 'getStoreBanner']);
    Route::get('get-store-notification', [NotificationController::class, 'getStoreNotification']);
    Route::post('post-store-like', [PopularController::class, 'postStoreLike']);
    Route::get('get-store-like', [PopularController::class, 'getStoreLike']);
    Route::post('change-password', [ChangePassword::class, 'changePassword']);
    //Route::post('get-business-view',[AnalyticController::class, 'getStoreBusinessView']);
    Route::post('logout',[AuthController::class, 'logout']);
});
Route::post('get-business-view',[AnalyticController::class, 'getStoreBusinessView']);


