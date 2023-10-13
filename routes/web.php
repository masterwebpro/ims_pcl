<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FileUploadController;
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
    Route::get('/receive/{id}/create', [App\Http\Controllers\ReceiveController::class, 'receivePo']);
    Route::delete('/receive', [App\Http\Controllers\ReceiveController::class, 'destroy']);
    Route::post('/receive/unpost', [App\Http\Controllers\ReceiveController::class, 'unpost']);

    Route::resource('/do', App\Http\Controllers\DeliveryOrderController::class);

    Route::resource('/withdraw', App\Http\Controllers\WithdrawalController::class);
    Route::delete('/withdraw', [App\Http\Controllers\WithdrawalController::class, 'destroy']);
    Route::post('/withdraw/unpost', [App\Http\Controllers\WithdrawalController::class, 'unpost']);
    Route::get('/picklist/{id}', [App\Http\Controllers\WithdrawalController::class, 'picklist']);
    Route::get('/withdrawalSlip/{id}', [App\Http\Controllers\WithdrawalController::class, 'withdrawalslip']);

    Route::resource('/dispatch', App\Http\Controllers\DispatchController::class);
    Route::delete('/dispatch', [App\Http\Controllers\DispatchController::class, 'destroy']);
    Route::post('/dispatch/unpost', [App\Http\Controllers\DispatchController::class, 'unpost']);

    Route::get('/deliverySlip/{id}', [App\Http\Controllers\DispatchController::class, 'deliveryslip']);
    Route::resource('/pod', App\Http\Controllers\PodController::class);
    Route::post('/upload-attachment', 'App\Http\Controllers\FileUploadController@upload');
    Route::resource('/expense', App\Http\Controllers\ExpenseController::class);

    Route::resource('/setting/suppliers', App\Http\Controllers\SuppliersController::class);

    //Maintenance
    Route::resource('/maintenance/unit', App\Http\Controllers\UnitOfMeasureController::class);
    Route::resource('/maintenance/warehouse', App\Http\Controllers\WarehouseController::class);
    Route::resource('/maintenance/supplier', App\Http\Controllers\SupplierController::class);
    Route::resource('/maintenance/client', App\Http\Controllers\ClientController::class);
    Route::resource('/maintenance/store', App\Http\Controllers\StoreController::class);
    Route::resource('/maintenance/brand', App\Http\Controllers\BrandController::class);
    Route::resource('/menu', App\Http\Controllers\MenuController::class);
    Route::resource('/maintenance/category', App\Http\Controllers\CategoryController::class);
    Route::resource('/maintenance/location', App\Http\Controllers\StorageLocationController::class);
    Route::resource('/maintenance/product', App\Http\Controllers\ProductController::class);
    Route::resource('/maintenance/attributes', App\Http\Controllers\AttributesController::class);
    Route::resource('/maintenance/trucker', App\Http\Controllers\TruckerController::class);
    Route::resource('/maintenance/particulars', App\Http\Controllers\ParticularController::class);

    Route::resource('/users', App\Http\Controllers\UsersController::class);
    Route::get('/productTemplate', 'App\Http\Controllers\ProductController@productTemplate');
    Route::post('/uploadProduct', 'App\Http\Controllers\ProductController@uploadProduct');
    Route::resource('/master', App\Http\Controllers\MasterDataController::class);

});

Route::group(['prefix' => 'settings', 'middleware' => 'auth'], function () {
    Route::get('/getStore', [App\Http\Controllers\SettingsController::class, 'getStoreByClient']);
    Route::get('/getWarehouse', [App\Http\Controllers\SettingsController::class, 'getWarehouseByStore']);
    Route::get('/products/{supplier_id}/get', [App\Http\Controllers\SettingsController::class, 'getProductBySupplier']);
    Route::get('/products', [App\Http\Controllers\SettingsController::class, 'getProducts']);
    Route::get('/uom', [App\Http\Controllers\SettingsController::class, 'getUom']);
    Route::get('/getPostedPO', [App\Http\Controllers\SettingsController::class, 'getPostedPo']);
    Route::get('/getAllPostedPO', [App\Http\Controllers\SettingsController::class, 'getAllPostedPo']);
    Route::get('/getBrand', [App\Http\Controllers\SettingsController::class, 'getBrandByCategory']);
    Route::get('/getEntity', [App\Http\Controllers\SettingsController::class, 'getAttributeEntity']);
    Route::get('/getCategoryAttribute', [App\Http\Controllers\SettingsController::class, 'getCategoryAttribute']);
    Route::get('/getProductAttribute', [App\Http\Controllers\SettingsController::class, 'getProductAttribute']);
    Route::get('/getLevel', [App\Http\Controllers\SettingsController::class, 'getLevel']);
    Route::get('/getStorageLocationId', [App\Http\Controllers\SettingsController::class, 'getStorageLocation']);

    Route::get('/masterfile', [App\Http\Controllers\SettingsController::class, 'getMasterfileData']);
    Route::get('/newLocation/{warehouse_id}', [App\Http\Controllers\SettingsController::class, 'getNewLocation']);

    Route::get('/available_item', [App\Http\Controllers\SettingsController::class, 'getAvailableItem']);
    Route::get('/withdrawal_list', [App\Http\Controllers\SettingsController::class, 'getWithdrawalList']);
    Route::get('/withdrawalDetails', [App\Http\Controllers\SettingsController::class, 'withdrawalDetails']);
    Route::get('/getTruckType', [App\Http\Controllers\SettingsController::class, 'getTruckType']);
    Route::get('/getPlateNo', [App\Http\Controllers\SettingsController::class, 'getPlateNo']);

    Route::get('/product', [App\Http\Controllers\SettingsController::class, 'getProduct']);
    Route::get('/allProducts', [App\Http\Controllers\SettingsController::class, 'getAllProduct']);
    Route::get('/_encode/{value}', [App\Http\Controllers\SettingsController::class, '_encode']);

    Route::get('/getLocation', [App\Http\Controllers\SettingsController::class, 'getLocation']);

    Route::get('/getAllPostedDO', [App\Http\Controllers\SettingsController::class, 'getAllPostedDo']);
    Route::get('/getAvailableStocks', [App\Http\Controllers\SettingsController::class, 'getAvailableStocks']);
    Route::get('/getParticulars', [App\Http\Controllers\SettingsController::class, 'getParticulars']);
    Route::get('/getAllPostedDispatch', [App\Http\Controllers\SettingsController::class, 'getAllPostedDispatch']);

});

Route::group(['prefix' => 'stock', 'middleware' => 'auth'], function () {
    Route::resource('/movement', App\Http\Controllers\StockMovementController::class);
    Route::delete('/movement', [App\Http\Controllers\StockMovementController::class, 'destroy']);
    Route::post('/movement/unpost', [App\Http\Controllers\StockMovementController::class, 'unpost']);

    Route::post('/movement/validate', [App\Http\Controllers\StockMovementController::class, 'getValidate']);
    Route::resource('/adjustment', App\Http\Controllers\StockAdjustmentController::class);
    Route::resource('/count-sheet', App\Http\Controllers\CountSheetController::class);
});

Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {
    Route::get('/stock-ledger', [App\Http\Controllers\ReportController::class, 'getStockLedgerIndex']);
    Route::get('/receiving-detailed', [App\Http\Controllers\ReportController::class, 'getReceivingDetailedIndex']);
    Route::get('/get-receiving-detailed', [App\Http\Controllers\ReportController::class, 'getReceivingDetailed']);
    Route::get('/export-receiving-detailed',[App\Http\Controllers\ReportController::class,'exportReceivingDetailed'])->name('export-receiving-detailed');
    Route::get('/print-receiving-detailed',[App\Http\Controllers\ReportController::class,'printPdfReceivingDetailed'])->name('print-receiving-detailed');
    Route::get('/getStockLedger',[App\Http\Controllers\ReportController::class,'getStockLedger'])->name('getStockLedger');

    Route::get('/inventory',[App\Http\Controllers\ReportController::class,'inventory'])->name('report.inventory');
    Route::get('/getInventoryReport',[App\Http\Controllers\ReportController::class,'getInventoryReport'])->name('report.getInventoryReport');

    Route::get('/export-inventory',[App\Http\Controllers\ReportController::class,'exportInventory'])->name('exportInventory');

    Route::get('/withdrawal-detailed', [App\Http\Controllers\ReportController::class, 'getWithdrawalDetailedIndex']);
    Route::get('/get-withdrawal-detailed', [App\Http\Controllers\ReportController::class, 'getWithdrawalDetailed']);
    Route::get('/export-withdrawal-detailed',[App\Http\Controllers\ReportController::class,'exportWithdrawalDetailed'])->name('export-withdrawal-detailed');
    Route::get('/print-withdrawal-detailed',[App\Http\Controllers\ReportController::class,'printPdfWithdrawalDetailed'])->name('print-withdrawal-detailed');
});



Route::get('/setting/suppliers/getdata', [App\Http\Controllers\SuppliersController::class, 'getDataTableData'])->name('getdata');
Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
