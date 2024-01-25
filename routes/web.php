<?php

use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\Dashboard\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin/dashboard')->middleware(['auth', 'isAdmin'])->group(function () {
    // Define your admin routes here

    // Example admin dashboard route
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/category', CategoriesController::class, ['names' => [
        'index'=>'admin.category',
        'create' => 'admin.category.create',
        'edit' => 'admin.category.edit'
    ]]);
    Route::resource('/brand', BrandsController::class, ['names' => [
        'index'=>'admin.brand',
        'create' => 'admin.brand.create',
        'edit' => 'admin.brand.edit'
    ]]);
    Route::resource('/product', ProductsController::class, ['names' => [
        'index'=>'admin.product',
        'create' => 'admin.product.create',
        'edit' => 'admin.product.edit'
    ]]);

    Route::get('/receipt', [ReceiptController::class, 'index'])->name('admin.receipt');
    Route::get('/confirm-receipt/{id}', [ReceiptController::class, 'confirmReceipt'])->name('admin.confirm-receipt');
    Route::get('/delete-receipt/{id}', [ReceiptController::class, 'deleteReceipt'])->name('admin.delete-receipt');
    Route::get('/receipt/dynamicModal/{id}', [ReceiptController::class, 'loadModal'])->name('dynamicModal');

    
    Route::get('/orderDetails', [OrderController::class, 'getOrderDetails'])->name('admin.orderDetails');
    Route::get('/{reference}/{id?}/order', [OrderController::class, 'getOrder'])->name('admin.order');
    Route::get('/{reference}/order', [OrderController::class, 'getGuestOrder'])->name('admin.guest-order');
    Route::get('/guestorderDetails', [OrderController::class, 'getGuestOrderDetails'])->name('admin.guestOrderDetails');

    
    Route::get('export-product', [ProductsController::class, 'exportProductByCategory'])->name('viewExportPage');
    Route::get('fetch-product', [ProductsController::class, 'fetchProductByCategory'])->name('admin.fetchProduct');

    Route::get('/customers', [DashboardController::class, 'getCustomers'])->name('admin.users');
    Route::get('/guests', [DashboardController::class, 'getGuests'])->name('admin.guest');


    Route::get('/report-page', [ReportController::class,'reportPage'])->name('admin.report');
    Route::get('/bulk-report-page', [ReportController::class,'reportBulkPage'])->name('admin.bulk-report');
    Route::get('/daily-report-page', [ReportController::class,'dailyReportPage'])->name('admin.daily-report');
    Route::get('/monthly-report-page', [ReportController::class,'monthlyReportPage'])->name('admin.monthly-report');
    Route::get('/yearly-report-page', [ReportController::class,'yearlyReportPage'])->name('admin.yearly-report');
    Route::get('/daily-bulk-report-page', [ReportController::class,'dailyBulkReportPage'])->name('admin.bulk-daily-report');
    Route::get('/monthly-bulk-report-page', [ReportController::class,'monthlyBulkReportPage'])->name('admin.bulk-monthly-report');
    Route::get('/yearly-bulk-report-page', [ReportController::class,'yearlyBulkReportPage'])->name('admin.bulk-yearly-report');
    Route::post('/daily-report-page', [ReportController::class,'getDailyReport'])->name('admin.get-daily-report');
    Route::post('/monthly-report-page', [ReportController::class,'getMonthlyReport'])->name('admin.get-monthly-report');
    Route::post('/yearly-report-page', [ReportController::class,'getYearlyReport'])->name('admin.get-yearly-report');
    Route::post('/daily-report-bulk-page', [ReportController::class,'getDailyBulkReport'])->name('admin.get-daily-bulk-report');
    Route::post('/monthly-report-bulk-page', [ReportController::class,'getMonthlyBulkReport'])->name('admin.get-daily-bulk-report');
    Route::post('/yearly-report-bulk-page', [ReportController::class,'getYearlyBulkReport'])->name('admin.get-daily-bulk-report');

    

});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
