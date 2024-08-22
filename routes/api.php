<?php

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

Route::controller(App\Http\Controllers\API\AuthOtpController::class)->group(function(){
    Route::post('otp/generate', 'generate');
    Route::post('login', 'loginWithOtp');
    Route::post('register', 'registerWithOtp');
    Route::post('logout', 'logout')->middleware(['auth:sanctum', 'check.token']);
});

Route::middleware(['auth:sanctum', 'check.token'])->group(function () {
    Route::apiResource('products', App\Http\Controllers\API\ProductController::class);
    Route::get('product/search', [App\Http\Controllers\API\ProductController::class , 'search']);
    Route::get('product/category/{category_id}', [App\Http\Controllers\API\ProductController::class , 'getByCategory']);
    
    Route::apiResource('categories', App\Http\Controllers\API\CategoryController::class);
    Route::get('/categories/{id}/subcategories', [App\Http\Controllers\API\CategoryController::class, 'getSubcategories']);

    Route::apiResource('users', App\Http\Controllers\API\UserController::class);

    Route::apiResource('shops', App\Http\Controllers\API\ShopController::class);

    // Cart routes
    Route::apiResource('carts', App\Http\Controllers\API\CartController::class);
    Route::post('cart/add', [App\Http\Controllers\API\CartController::class, 'addToCart']);
    Route::get('cart', [App\Http\Controllers\API\CartController::class, 'getCart']);

    // Order routes
    Route::apiResource('orders', App\Http\Controllers\API\OrderController::class);
    Route::get('user/orders', [App\Http\Controllers\API\OrderController::class, 'getUserOrders']);

    // Address routes
    Route::apiResource('addresses', App\Http\Controllers\API\AddressController::class);

    // Regions routes
    Route::apiResource('regions', App\Http\Controllers\API\RegionController::class);
});