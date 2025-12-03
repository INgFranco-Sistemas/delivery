<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Categorías
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Productos (tortas)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // 🟢 PEDIDOS

    // Cliente crea pedido
    Route::post('/orders', [OrderController::class, 'store']);

    // Cliente ve solo sus pedidos
    Route::get('/my-orders', [OrderController::class, 'myOrders']);

    // Admin ve todos los pedidos
    Route::get('/orders', [OrderController::class, 'index']);

    // Ver detalle de un pedido (admin o dueño)
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Admin cambia estado
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});
