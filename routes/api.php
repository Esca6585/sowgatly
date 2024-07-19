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

/**
 * @OA\Info(
 *     title="Your API Name",
 *     version="1.0.0",
 *     description="Your API Description"
 * )
 */

/**
 * @OA\Post(
 *     path="/api/otp/generate",
 *     summary="Generate OTP",
 *     tags={"Authentication"}
 * )
 */

/**
 * @OA\Post(
 *     path="/api/otp/login",
 *     summary="Login with OTP",
 *     tags={"Authentication"}
 * )
 */

/**
 * @OA\Info(
 *     title="Your API Name",
 *     version="1.0.0",
 *     description="Your API Description"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="List all products",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
 *     )
 * )
 */

/**
 * @OA\Post(
 *     path="/api/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get a specific product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

/**
 * @OA\Put(
 *     path="/api/products/{id}",
 *     summary="Update a specific product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Delete a specific product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Successful operation"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
 
Route::controller(App\Http\Controllers\API\AuthOtpController::class)->group(function(){
    Route::post('otp/generate', 'generate')->name('otp.generate');
    
    Route::post('otp/login', 'loginWithOtp')->name('otp.getlogin');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', App\Http\Controllers\API\ProductController::class);
    Route::get('/user', [App\Http\Controllers\Admin\Api\AdminController::class, 'user']);
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
