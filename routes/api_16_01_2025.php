<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,CategoryController,SearchController,ForgotPasswordController,PopularController,NotificationController,ChangePassword,AnalyticController,ProductController,RegisteredUserController,ArtistController,HomeController,ProfileController};
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
Route::post('/login', [RegisteredUserController::class, 'login']);

Route::post('/google-callback', [AuthController::class, 'googleLogin']);

//Forget Password
Route::post('send-link-forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']); 

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');

Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

//----------------------------
Route::post('store-customer', [RegisteredUserController::class, 'store_customer'])->name('store-customer');
Route::post('store-retailer', [RegisteredUserController::class, 'store_retailer'])->name('store-retailer');
Route::post('forgot-password', [RegisteredUserController::class, 'forgotPassword'])->name('forgot-password');
Route::post('forgot-password-verify-otp', [RegisteredUserController::class, 'forget_password_verify_otp'])->name('forgot-password-verify-otp');
Route::post('reset-password', [RegisteredUserController::class, 'resetpassword'])->name('reset-password');

Route::post('register-verify-otp', [RegisteredUserController::class, 'register_verify_otp'])->name('register-verify-otp');
//---------------------------

Route::post('category-list',[CategoryController::class, 'getCategories']);
Route::post('get-subcategory-list',[CategoryController::class, 'get_subcategory_list']);
Route::post('get-product-list',[ProductController::class, 'get_product_list']);
Route::post('get-artist-list',[ArtistController::class, 'get_artist_list']);
Route::post('single-product',[ProductController::class, 'get_single_product']);
Route::post('product-search',[ProductController::class, 'get_product_search']);

Route::get('home-category-list',[HomeController::class, 'category_list']);
Route::get('home-artist-list',[HomeController::class, 'artist_list']);
Route::get('view-profile',[ProfileController::class, 'view_profile']);
Route::post('/profile-image-upload',[ProfileController::class, 'profile_image_upload']);
Route::get('/profile-image-delete',[ProfileController::class, 'profile_image_delete']);

Route::post('/edit-customer-profile',[RegisteredUserController::class, 'edit_customer_profile']);
Route::post('/edit-retailer-profile',[RegisteredUserController::class, 'edit_retailer_profile']);
Route::post('/change-user-password',[RegisteredUserController::class, 'change_password']);
//Route::middleware('auth:api')->get('/view-profile', [ProfileController::class, 'view_profile']);


Route::group(['middleware' => ['user']], function() {
    ///Route::get('categories',[CategoryController::class, 'getCategories']);
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
	//Route::get('category-list', [CategoryController::class, 'getCategories']);
	//Route::get('view-profile',[ProfileController::class, 'view_profile']);
    Route::post('logout',[AuthController::class, 'logout']);
});
Route::post('get-business-view',[AnalyticController::class, 'getStoreBusinessView']);


