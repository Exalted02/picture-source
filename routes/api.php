<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisteredUserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\DeliveryAddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WistlistController;
use App\Http\Controllers\Api\CountryStateController;

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

Route::post('register-verify-otp', [RegisteredUserController::class, 'register_verify_otp'])->name('register-verify-otp');

Route::post('category-list',[CategoryController::class, 'getCategories']);
Route::post('get-subcategory-list',[CategoryController::class, 'get_subcategory_list']);
Route::post('get-product-list',[ProductController::class, 'get_product_list']);
Route::post('get-artist-list',[ArtistController::class, 'get_artist_list']);
Route::get('single-product',[ProductController::class, 'get_single_product']);
Route::post('product-search',[ProductController::class, 'get_product_search']);

Route::get('home-category-list',[HomeController::class, 'category_list']);
Route::get('home-artist-list',[HomeController::class, 'artist_list']);
Route::post('view-profile',[ProfileController::class, 'view_profile']);
Route::post('/profile-image-upload',[ProfileController::class, 'profile_image_upload']);
Route::get('/profile-image-delete',[ProfileController::class, 'profile_image_delete']);

Route::post('/edit-customer-profile',[RegisteredUserController::class, 'edit_customer_profile']);
Route::post('/edit-retailer-profile',[RegisteredUserController::class, 'edit_retailer_profile']);
Route::post('/change-user-password',[RegisteredUserController::class, 'change_password']);

Route::post('/give-review',[ReviewController::class, 'give_review']);
Route::post('/count-total-rating',[ReviewController::class, 'count_total_rating']);
Route::post('/count-total-review',[ReviewController::class, 'count_total_review']);
Route::post('/total-avg-rating-product',[ReviewController::class, 'total_avg_rating_product']);
Route::post('/percentage-cal-rating-products',[ReviewController::class, 'percentage_cal_rating_products']);

Route::get('/size-list',[SizeController::class, 'size_list']);
Route::get('/color-list',[ColorController::class, 'color_list']);

Route::post('/add-delivery-address',[DeliveryAddressController::class, 'add']);
Route::post('/edit-delivery-address',[DeliveryAddressController::class, 'edit']);
Route::get('/list-delivery-address',[DeliveryAddressController::class, 'delivery_address_list']);
Route::post('/delete-delivery-address',[DeliveryAddressController::class, 'delete']);
Route::post('/place-order',[OrderController::class, 'place_order']);
Route::post('/create-wistlist',[WistlistController::class, 'create_wistlist']);

Route::get('/my-order',[OrderController::class, 'my_order']);
Route::post('/my-order-details',[OrderController::class, 'my_order_details']);
Route::get('/my-wistlist',[OrderController::class, 'my_wistlist']);
Route::post('/my-wistlist-order-details',[OrderController::class, 'my_wistlist_order_details']);

Route::get('/country_list',[CountryStateController::class, 'country_list']);
Route::post('/state_list',[CountryStateController::class, 'state_list']);
Route::post('/city_list',[CountryStateController::class, 'city_list']);

Route::get('/gender_list',[ProfileController::class, 'gender_list']);

//Route::get('reset-password/{token}', [RegisteredUserController::class, 'showResetPasswordForm'])->name('reset.password.get');
//Route::post('reset-password', [RegisteredUserController::class, 'submitResetPasswordForm'])->name('reset.password.post');

