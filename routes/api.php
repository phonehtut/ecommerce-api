<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('JWTAuth')->group(function () {
    Route::get('/profile', [AuthController::class, 'getUser'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //User CRUD
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/user/{id}', 'detail')->name('user.detail');
        Route::post('/user/create', 'store')->name('user.store');
        Route::put('/user/{id}/update', 'update')->name('user.update');
        Route::delete('/user/{id}/delete', 'destroy')->name('user.destroy');
    });

    // Product CRUD
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products')->withoutMiddleware('JWTAuth');
        Route::post('/product/create', 'store')->name('product.store');
        Route::get('/product/{products:slug}', 'detail')->name('product.detail')->withoutMiddleware('JWTAuth');
        Route::put('/product/{products:slug}/update', 'update')->name('product.update');
        Route::delete('/product/{products:slug}/delete', 'destroy')->name('product.destroy');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index')->name('categories')->withoutMiddleware('JWTAuth');
        Route::post('/category/create', 'store')->name('category.store');
        Route::get('/category/{id}/detail', 'detail')->name('category.detail')->withoutMiddleware('JWTAuth');
        Route::put('/category/{id}/update', 'update')->name('category.update');
        Route::delete('/category/{id}/delete', 'destroy')->name('category.destroy');
    });
});
