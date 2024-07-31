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
    Route::post('otp/login', 'loginWithOtp');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', App\Http\Controllers\API\ProductController::class);
    Route::get('product/search', [App\Http\Controllers\API\ProductController::class , 'search']);
    Route::get('product/category/{category_id}', [App\Http\Controllers\API\ProductController::class , 'getByCategory']);
    
    Route::apiResource('categories', App\Http\Controllers\API\CategoryController::class);
    Route::get('/categories/{id}/subcategories', [App\Http\Controllers\API\CategoryController::class, 'getSubcategories']);

    Route::apiResource('users', App\Http\Controllers\API\UserController::class);

    Route::apiResource('shops', App\Http\Controllers\API\ShopController::class);
});

Route::post('/register', [App\Http\Controllers\User\Auth\AuthController::class, 'register']);

Route::post('/login', [App\Http\Controllers\User\Auth\AuthController::class, 'login']);

Route::post('/logout', [App\Http\Controllers\User\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/me', [App\Http\Controllers\User\Auth\AuthController::class, 'me'])->middleware('auth:sanctum');

// Admin Login API

Route::post('/admin/login', [App\Http\Controllers\Admin\Auth\AuthController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Admin\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');

// Admin App API
Route::get('/users/{search?}', [App\Http\Controllers\Admin\Api\AdminController::class, 'users'])->middleware('auth:sanctum');
Route::get('/user/statistic', [App\Http\Controllers\Admin\Api\AdminController::class, 'userStatistic'])->middleware('auth:sanctum');

Route::get('/user/{id}',[App\Http\Controllers\Admin\Api\AdminController::class, 'user'])->middleware('auth:sanctum');
Route::get('/user/delete/{id}', [App\Http\Controllers\Admin\Api\AdminController::class, 'userDelete'])->middleware('auth:sanctum');
Route::post('/user/create', [App\Http\Controllers\Admin\Api\AdminController::class, 'userCreate'])->middleware('auth:sanctum');
Route::get('/user-block-unblock/{id}', [App\Http\Controllers\Admin\Api\AdminController::class, 'userBlockUnblock'])->middleware('auth:sanctum');
Route::post('/user-update', [App\Http\Controllers\Admin\Api\AdminController::class, 'userUpdate'])->middleware('auth:sanctum');

Route::get('/category', [App\Http\Controllers\Admin\Api\AdminController::class, 'categories'])->middleware('auth:sanctum');
Route::get('/category/{id}',[App\Http\Controllers\Admin\Api\AdminController::class, 'category'])->middleware('auth:sanctum');
Route::get('/category/delete/{id}', [App\Http\Controllers\Admin\Api\AdminController::class, 'categoryDelete'])->middleware('auth:sanctum');
Route::post('/category/create', [App\Http\Controllers\Admin\Api\AdminController::class, 'categoryCreate'])->middleware('auth:sanctum');
Route::post('/category-update', [App\Http\Controllers\Admin\Api\AdminController::class, 'categoryUpdate'])->middleware('auth:sanctum');

Route::post('/send-notification', [App\Http\Controllers\Admin\Api\AdminController::class, 'sendNotification'])->middleware('auth:sanctum');
