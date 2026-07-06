<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class,'index'])
    ->name('dashboard');
Route::get('/login',[AuthController::class,'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/', [LandingController::class, 'index']);