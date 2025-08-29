<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/shop/create', [ShopController::class, 'create'])->name('shop.create');
    Route::post('/admin/shop', [ShopController::class, 'store'])->name('shop.store');
});
Route::middleware(['auth', 'role:admin|vendedor'])->group(function () {
    // Rutas para productos (crear, editar, eliminar)
    Route::get('/admin/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');
    // ... otras rutas de gestión de productos
});
