<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('api.password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.password.update');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/admin/users', [UserController::class, 'list'])->name('api.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('api.users.store');
});
