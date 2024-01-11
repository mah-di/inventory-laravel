<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/request-otp', [UserController::class, 'sendOTP'])->name('send.otp');
Route::post('/verify-otp', [UserController::class, 'verifyOTP'])->name('verify.otp');

Route::middleware('auth.jwt')->group(function () {
    Route::post('/verify-email', [UserController::class, 'verifyEmail'])->name('verify.email');
    Route::get('/resend-email-verification-code', [UserController::class, 'resendEmailVerificationOTP'])->name('resend.emailVerification');
    Route::patch('/reset-password', [UserController::class, 'resetPassword'])->name('password.reset');
    Route::patch('/change-password', [UserController::class, 'changePassword'])->name('password.change');
    Route::patch('/user', [UserController::class, 'update'])->name('update.user');
    Route::get('/user-details', [UserController::class, 'getUser'])->name('user.details');

    Route::controller(CategoryController::class)->prefix('/category')->group(function () {
        Route::get('', 'all')->name('category.all');
        Route::get('/{id}', 'find')->name('category.single');
        Route::post('', 'store')->name('category.create');
        Route::patch('', 'update')->name('category.update');
        Route::delete('', 'delete')->name('category.delete');
    });
});
