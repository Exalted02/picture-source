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
	
	// product code 
	Route::get('/category', [CategoryController::class, 'index'])->name('user.category');
	Route::post('/save-category', [CategoryController::class, 'save_category'])->name('user.save-category');
	Route::post('/category-update-status',[CategoryController::class,'update_status'])->name('category-update-status');
	Route::post('/edit-category',[CategoryController::class,'edit_category'])->name('edit-category');
	Route::post('/getDeleteCategory',[CategoryController::class,'delete_category'])->name('getDeleteCategory');
	Route::post('/deleteCategoryList',[CategoryController::class,'delete_category_list'])->name('deleteCategoryList');
	Route::post('/category', [CategoryController::class, 'index'])->name('user.category');
	
	// product group 
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
	
// Email Management Routes
	Route::get('email-management', [EmailManagementController::class,'index'])->name('email-management');
	Route::get('/email-management-edit/{id}', [EmailManagementController::class, 'email_management_edit'])->name('email-management-edit');
	Route::post('/email-management-edit-save',[EmailManagementController::class,'manage_email_management_process'])->name('email-management-edit-save');	
	
});

require __DIR__.'/auth.php';
