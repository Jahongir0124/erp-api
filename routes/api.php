<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
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
    Route::post('user/{id}/restore', 
    [UserController::class, 'restore']);
    Route::delete('user/{id}/forceDelete',
    [UserController::class, 'forceDelete']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('category', CategoryController::class);
});



