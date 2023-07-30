<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
Route::get('/dashboard/2', [App\Http\Controllers\DashboardController::class, 'index'])->name('index');


Route::group(['middleware' => 'auth'], function () {
    Route::resource('/po', App\Http\Controllers\PurchaseOrderController::class);
    Route::resource('/receive', App\Http\Controllers\ReceiveController::class);

    Route::resource('/setting/suppliers', App\Http\Controllers\SuppliersController::class);

    //Maintenance
    Route::resource('/maintenance/unit', App\Http\Controllers\UnitOfMeasureController::class);
    Route::resource('/maintenance/warehouse', App\Http\Controllers\WarehouseController::class);
    Route::resource('/maintenance/supplier', App\Http\Controllers\SupplierController::class);
    Route::resource('/maintenance/client', App\Http\Controllers\ClientController::class);
    Route::resource('/maintenance/store', App\Http\Controllers\StoreController::class);
    Route::resource('/maintenance/brand', App\Http\Controllers\BrandController::class);

});






Route::get('/setting/suppliers/getdata', [App\Http\Controllers\SuppliersController::class, 'getDataTableData'])->name('getdata');


// Route::get('/suppliers', [App\Http\Controllers\SuppliersController::class, 'index'])->name('index');
// Route::get('/suppliers/create', [App\Http\Controllers\SuppliersController::class, 'create'])->as('supplier.create');
// Route::get('/suppliers/store', [App\Http\Controllers\SuppliersController::class, 'store'])->name('store');

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

Route::group(['prefix' => 'settings', 'middleware' => 'auth'], function () {
    // Route::get('/supplier', [App\Http\Controllers\SettingsController::class, 'getSupplier']);
    // Route::get('/client', [App\Http\Controllers\SettingsController::class, 'getClient']);
    // Route::get('/store', [App\Http\Controllers\SettingsController::class, 'getStore']);

    Route::get('/getStore', [App\Http\Controllers\SettingsController::class, 'getStoreByClient']);
    Route::get('/getWarehouse', [App\Http\Controllers\SettingsController::class, 'getWarehouseByStore']);
    Route::get('/products', [App\Http\Controllers\SettingsController::class, 'getProducts']);
    Route::get('/uom', [App\Http\Controllers\SettingsController::class, 'getUom']);


});
