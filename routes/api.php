<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('users/{id}/restore', 
    [UserController::class, 'restore']);
    Route::delete('users/{id}/forceDelete',
    [UserController::class, 'forceDelete']);
    Route::patch('users/{user}/role',
    [UserController::class, 'updateRole']);
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('category', CategoryController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('product', ProductController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('roles', RoleController::class);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('orders', OrderController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('customers', CustomerController::class);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post(
        '/products/{product}/images',
        [ProductImageController::class, 'store']
    );
    Route::delete(
        '/products/{product}/{image}',
        [ProductImageController::class, 'destroy']
    );
    Route::patch(
        '/products/{product}/images/{image}/primary',
        [ProductImageController::class, 'setPrimary']
        );
});

Route::middleware('auth:sanctum')->group(function () {

    Route::patch(
        'orders/{order}/confirm',
        [OrderController::class, 'confirm']
    );
    Route::patch(
        'orders/{order}/pending/cancel',
        [OrderController::class, 'cancelPending']
    );
    Route::patch(
        'orders/{order}/confirmed/cancel',
        [OrderController::class, 'cancelConfirmed']
    );
    Route::patch(
        'orders/{order}/complete',
        [OrderController::class, 'complete']
    );
});








