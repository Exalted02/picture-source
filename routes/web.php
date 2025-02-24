<?php

use Illuminate\Http\Request;

use App\Http\Controllers\LangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;

use App\Http\Controllers\EmailManagementController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\RegisteredUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('clear-cache', function () {
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
	\Artisan::call('cache:clear');
    // \Artisan::call('route:cache');
    \Artisan::call('view:clear');
    \Artisan::call('config:cache');
    \Artisan::call('optimize:clear');
	Log::info('Clear all cache');
    dd("Cache is cleared");
});
Route::get('db-migrate', function () {
    \Artisan::call('migrate');
    dd("Database migrated");
});
Route::get('db-seed', function () {
    \Artisan::call('db:seed');
    dd("Database seeded");
});
Route::get('/', [ProfileController::class, 'welcome']);

Route::get('lang/home', [LangController::class, 'index']);
Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
	


Route::middleware('auth')->group(function () {
	//ChangePassword
	Route::get('/change-password', [ChangePasswordController::class, 'index'])->name('change-password');
	Route::post('/change-password', [ChangePasswordController::class, 'save_data'])->name('change-password-save');

	//EmailSettings
	Route::get('/email-settings', [EmailSettingsController::class, 'index'])->name('user.email-settings');
	Route::post('/email-settings', [EmailSettingsController::class, 'save_data'])->name('email-settings-save');
	
	// category 
	Route::get('/category', [CategoryController::class, 'index'])->name('user.category');
	Route::post('/save-category', [CategoryController::class, 'save_category'])->name('user.save-category');
	Route::post('/category-update-status',[CategoryController::class,'update_status'])->name('category-update-status');
	Route::post('/edit-category',[CategoryController::class,'edit_category'])->name('edit-category');
	Route::post('/getDeleteCategory',[CategoryController::class,'delete_category'])->name('getDeleteCategory');
	Route::post('/deleteCategoryList',[CategoryController::class,'delete_category_list'])->name('deleteCategoryList');
	Route::post('/category', [CategoryController::class, 'index'])->name('user.category');
	
	Route::post('/category-dropzone', [CategoryController::class, 'dropzone_store'])->name('category.dropzone.store');
	Route::post('/delete-category-media', [CategoryController::class, 'delete_media'])->name('delete.category.media');
	
	// subcategory
	Route::get('/subcategory', [SubcategoryController::class, 'index'])->name('user.subcategory');
	Route::post('/save-subcategory', [SubcategoryController::class, 'save_subcategory'])->name('user.save-subcategory');
	Route::post('/subcategory-update-status',[SubcategoryController::class,'update_status'])->name('subcategory-update-status');
	Route::post('/edit-subcategory',[SubcategoryController::class,'edit_subcategory'])->name('edit-subcategory');
	Route::post('/getDeleteSubcategory',[SubcategoryController::class,'delete_subcategory'])->name('getDeleteSubcategory');
	Route::post('/deleteSubcategoryList',[SubcategoryController::class,'delete_subcategory_list'])->name('deleteSubcategoryList');	
	Route::post('/subcategory', [SubcategoryController::class, 'index'])->name('user.subcategory');

	Route::post('/change-multi-status',[CommonController::class,'change_multi_status'])->name('change-multi-status');
	Route::post('/delete-multi-data',[CommonController::class,'delete_multi_data'])->name('delete-multi-data');
	Route::post('/getstatebycountry',[CommonController::class,'get_state_by_country'])->name('getstatebycountry');
	Route::post('/getcitybystate',[CommonController::class,'get_city_by_state'])->name('getcitybystate');
	
	Route::post('/subcategory-dropzone', [SubcategoryController::class, 'dropzone_store'])->name('subcategory.dropzone.store');
	Route::post('/delete-media', [SubcategoryController::class, 'delete_media'])->name('delete.subcategory.media');
	
// Email Management Routes
	Route::get('email-management', [EmailManagementController::class,'index'])->name('email-management');
	Route::get('/email-management-edit/{id}', [EmailManagementController::class, 'email_management_edit'])->name('email-management-edit');
	Route::post('/email-management-edit-save',[EmailManagementController::class,'manage_email_management_process'])->name('email-management-edit-save');
	
	// customer
	Route::get('customer', [CustomerController::class,'customer_list'])->name('customer');
	Route::post('/customer-update-status',[CustomerController::class,'update_status'])->name('customer-update-status'); 
	Route::post('/getDeleteCustomer',[CustomerController::class,'delete_customer'])->name('getDeleteCustomer');
	Route::post('/deleteCustomerList',[CustomerController::class,'delete_customer_list'])->name('deleteCustomerList');
	Route::get('/view-customer/{id}', [CustomerController::class, 'view_customer'])->name('view-customer');
	Route::get('/view-customer-order-details/{id}', [CustomerController::class, 'view_customer_order_details'])->name('view-customer-order-details');
	
	// retailer  view-retailer
	Route::get('retailer', [RetailerController::class,'retailer_list'])->name('retailer');
	Route::post('/retailer-update-status',[RetailerController::class,'update_status'])->name('retailer-update-status'); 
	Route::post('/getDeleteRetailer',[RetailerController::class,'delete_retailer'])->name('getDeleteRetailer');
	Route::post('/deleteRetailerList',[RetailerController::class,'delete_retailer_list'])->name('deleteRetailerList');
	Route::get('/retailer-tax-download',[RetailerController::class,'retailer_tax_download'])->name('retailer-tax-download');
	Route::get('/view-retailer/{id}', [RetailerController::class, 'view_retailer'])->name('view-retailer');
	Route::get('/view-retailer-list/{id}', [RetailerController::class, 'view_retailer_list'])->name('view-retailer-list');
	
	// artists
	Route::get('artists', [ArtistController::class,'index'])->name('artists');
	Route::post('/save-artist', [ArtistController::class, 'save_artist'])->name('user.save-artist');
	Route::post('/artist-update-status',[ArtistController::class,'update_status'])->name('artist-update-status');
	Route::post('/edit-artist',[ArtistController::class,'edit_artist'])->name('edit-artist');
	Route::post('/getDeleteArtist',[ArtistController::class,'delete_artist'])->name('getDeleteArtist');
	Route::post('/deleteArtistList',[ArtistController::class,'delete_artist_list'])->name('deleteArtistList');
	Route::post('/artist', [ArtistController::class, 'index'])->name('user.artist');
	Route::post('/del-artist-image', [ArtistController::class, 'del_artist_image'])->name('del-artist-image');
	
	// size
	Route::get('size', [SizeController::class,'index'])->name('size');
	Route::post('/save-size', [SizeController::class, 'save_size'])->name('user.save-size');
	Route::post('/size-update-status',[SizeController::class,'update_status'])->name('size-update-status');
	Route::post('/edit-size',[SizeController::class,'edit_size'])->name('edit-size');
	Route::post('/getDeleteSize',[SizeController::class,'delete_size'])->name('getDeleteSize');
	Route::post('/deleteSizeList',[SizeController::class,'delete_size_list'])->name('deleteSizeList');
	Route::post('/size', [SizeController::class, 'index'])->name('user.size');
	
	// color 
	Route::get('color', [ColorController::class,'index'])->name('color');
	Route::post('/save-color', [ColorController::class, 'save_color'])->name('user.save-color');
	Route::post('/color-update-status',[ColorController::class,'update_status'])->name('color-update-status');
	Route::post('/edit-color',[ColorController::class,'edit_color'])->name('edit-color');
	Route::post('/getDeleteColor',[ColorController::class,'delete_color'])->name('getDeleteColor');
	Route::post('/deleteColorList',[ColorController::class,'delete_color_list'])->name('deleteColorList');
	Route::post('/color', [ColorController::class, 'index'])->name('user.color');
	
	// products  
	Route::get('products', [ProductController::class,'index'])->name('products');
	//Route::post('products', [ProductController::class,'index'])->name('user.products');
	Route::post('/save-product', [ProductController::class, 'save_product'])->name('user.save-product');
	Route::post('/product-update-status',[ProductController::class,'update_status'])->name('product-update-status');
	Route::post('/edit-product',[ProductController::class,'edit_product'])->name('edit-product');
	Route::post('/getDeleteArtist',[ProductController::class,'delete_product'])->name('getDeleteArtist');
	Route::post('/deleteArtistList',[ProductController::class,'delete_product_list'])->name('deleteArtistList');
	Route::post('/product', [ProductController::class, 'index'])->name('user.product');
	
	Route::post('/product-dropzone', [ProductController::class, 'dropzone_store'])->name('product.dropzone.store');
	Route::post('/get-subcategory', [ProductController::class, 'get_subcategory'])->name('get-subcategory');
	
	Route::post('/delete-product-media', [ProductController::class, 'delete_product_media'])->name('delete.product.media');
	
	Route::get('/view-product/{id}', [ProductController::class, 'view_product'])->name('view-product');
	
	// order
	Route::get('order', [OrderController::class,'index'])->name('order');
	Route::post('order', [OrderController::class,'index'])->name('order');
	Route::get('/view-order/{id}', [OrderController::class, 'view_order'])->name('view-order');
	Route::post('change-order-status', [OrderController::class, 'change_order_status'])->name('change.order.status');
	
	// order wistlist
	Route::get('order-wistlist', [OrderController::class,'order_wistlist_list'])->name('order-wistlist');
	Route::post('order-wistlist', [OrderController::class,'order_wistlist_list'])->name('order-wistlist');
	Route::get('/view-order-wistlist/{id}', [OrderController::class, 'view_order_wistlist'])->name('view-order-wistlist');
	Route::post('change-wishlist-status', [OrderController::class, 'change_wishlist_status'])->name('change.wishlist.status');
	
	// account- remove 
	Route::get('account-remove', [CommonController::class,'account_remove'])->name('account-remove');
	Route::post('account-remove', [CommonController::class,'save_account_remove'])->name('account-remove');
	Route::get('account-remove-list', [CommonController::class,'account_remove_list'])->name('account-remove-list');
	Route::post('remove-account-update-status', [CommonController::class,'account_remove_update_status'])->name('remove-account-update-status');
	Route::post('remove-account-delete', [CommonController::class,'remove_account_delete'])->name('remove-account-delete');
	
	// notification 
	Route::get('notifications', [CommonController::class,'notifications'])->name('notifications');
	Route::get('notification-view/{id}', [CommonController::class,'notification_view'])->name('notification-view');
	
});



require __DIR__.'/auth.php';
