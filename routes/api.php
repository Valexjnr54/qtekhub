<?php

use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\Dashboard\CustomerDashboardController;
use App\Http\Controllers\Customer\Dashboard\WalletController;
use App\Http\Controllers\Customer\Dashboard\WishlistController;
use App\Http\Controllers\Frontend\BrandsController;
use App\Http\Controllers\Frontend\CategoriesController;
use App\Http\Controllers\Frontend\ProductsController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Payment\CheckoutController;
use App\Http\Controllers\Payment\VerifyOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function () {
    Route::middleware('api')->group(function () {
        // Customer authentication routes


        //Authentication Route
        Route::prefix('customers')->group(function () {
            Route::post('/customer-registration', [CustomerAuthController::class, 'register']);
            Route::post('/login-customer', [CustomerAuthController::class, 'login']);
            Route::get('/profile-customer', [CustomerAuthController::class, 'profile']);
            Route::get('/email-verification', [CustomerAuthController::class, 'email_verify']);
            Route::post('/logout-customer', [CustomerAuthController::class, 'logout']);
        });
        //Authentication Route Ends

        // Categories Route
        Route::prefix('categories')->group(function () {
            Route::get('/all-categories', [CategoriesController::class, 'index']);
            Route::get('/single-category', [CategoriesController::class, 'singleCategory']);
            Route::post('/category/search', [CategoriesController::class, 'searchCategory']);
            Route::get('/products-by-category', [CategoriesController::class, 'productByCategory']);
        });
        // Categories Route Ends

        // Brands Route
        Route::prefix('brands')->group(function () {
            Route::get('/all-brands', [BrandsController::class, 'index']);
            Route::get('/single-brand', [BrandsController::class, 'singleBrand']);
            Route::post('/brand/search', [BrandsController::class, 'searchBrand']);
        });
        // Brands Route Ends

        //product Route
        Route::prefix('products')->group(function () {
            Route::get('/all-products', [ProductsController::class, 'index']);
            Route::get('/single-product', [ProductsController::class, 'singleProduct']);
            Route::post('/product/search', [ProductsController::class, 'searchProduct']);
            Route::get('/product-brand', [ProductsController::class, 'productByBrand']);
            Route::get('/product-price-range', [ProductsController::class, 'priceRange']);
            Route::get('/product-sorting', [ProductsController::class, 'sortPrice']);
            Route::get('/product-brand-filtering', [ProductsController::class, 'brandFilter']);
            Route::get('/recommend-products', [ProductsController::class, 'recommendProducts']);
            Route::get('/latest-products', [ProductsController::class, 'latestProducts']);
        });
        //product Route Ends

        //General Search
        Route::prefix('search')->group(function () {
            Route::post('/search-all', [SearchController::class, 'searchAll']);
        });
        //General Search Ends

        //Checkout Route
        Route::prefix('checkout')->group(function () {
            Route::post('/paystack-payment-gateway', [CheckoutController::class, 'getPaystackLink']);
            Route::post('/upload-receipt', [CheckoutController::class, 'uploadReceipt']);
            Route::post('/wallet-payment-gateway', [WalletController::class, 'walletCheckout']);
            Route::get('/paystack-callback', [CheckoutController::class, 'paystackCallback']);
            Route::get('/verify-orders', [VerifyOrderController::class, 'verify']);
        });
        //Checkout Route Ends

        //Customer Dashboard Routes
        Route::prefix('customer/dashboard')->group(function () {
            Route::get('/customer-order', [CustomerDashboardController::class, 'orders']);
            Route::get('/customer-single-order', [CustomerDashboardController::class, 'singleOrders']);
            Route::get('/customer-refers', [CustomerDashboardController::class, 'refers']);
            Route::get('/customer-details', [CustomerDashboardController::class, 'details']);
            Route::post('/customer-change-password', [CustomerDashboardController::class, 'changePassword']);
            Route::post('/customer-change-location', [CustomerDashboardController::class, 'changeLocation']);
            Route::delete('/customer-delete-account', [CustomerDashboardController::class, 'deleteAccount']);
            Route::get('/customer-location', [CustomerDashboardController::class, 'customerLocation']);

            Route::post('/fund-wallet', [WalletController::class, 'fundWalletLink']);
            Route::get('/wallet-callback', [WalletController::class, 'fundWalletCallback']);

            Route::post('wishlist/add', [WishlistController::class,'addToWishlist']);
            Route::delete('wishlist/remove', [WishlistController::class,'removeFromWishlist']);
            Route::get('wishlist', [WishlistController::class,'getWishlist']);
        });
        //Customer Dashboard Routes Ends
    });
});