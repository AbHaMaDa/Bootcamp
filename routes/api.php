<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/user', UserController::class
    )->middleware('auth:sanctum');



//signning
Route::post('/register',[AuthController::class,'register'])->name("register");
Route::post('/login', [AuthController::class,'login'])->name("login");
Route::post('/logout', action: [AuthController::class,'logout'])->name("logout")->middleware('auth:sanctum');

// changing profile photo
Route::post('/avatar', action: [ImageController::class,'avatar'])->name("avatar")->middleware('auth:sanctum');



//products
Route::apiResource('/products', ProductController::class);



//categories
Route::apiResource('/categories', CategoryController::class);



//cart

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/update/{id}', [CartController::class, 'updateCart']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
});
