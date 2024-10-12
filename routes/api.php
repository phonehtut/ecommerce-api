<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('JWTAuth')->group(function () {
    Route::get('/profile', [AuthController::class, 'getUser'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/user/{id}', 'detail')->name('user.detail');
        Route::post('/user/create', 'store')->name('user.store');
        Route::put('/user/{id}/update', 'update')->name('user.update');
        Route::delete('/user/{id}/delete', 'destroy')->name('user.destroy');
    });
});
