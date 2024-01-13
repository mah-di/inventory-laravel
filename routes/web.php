<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'pages.home')->name('home.view');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'pages.auth.login-page')->name('login.view');
    Route::view('/register', 'pages.auth.registration-page')->name('register.view');
    Route::view('/request-otp', 'pages.auth.send-otp-page')->name('request.otp.view');
    Route::view('/verify-otp', 'pages.auth.verify-otp-page')->name('verify.otp.view');
});

Route::view('/verify-email', 'pages.auth.verify-email-page')->name('verify.email.view')->middleware('auth.jwt');

Route::middleware(['web.redirect', 'auth.jwt', 'verified.email'])->group(function () {
    Route::view('/reset-password', 'pages.auth.reset-pass-page')->name('password.reset.view');
    Route::view('/change-password', 'pages.auth.change-pass-page')->name('password.change.view');
    Route::view('/dashboard', 'pages.dashboard.dashboard-page')->name('dashboard.view');
    Route::view('/profile', 'pages.dashboard.profile-page')->name('profile.view');
    Route::view('/category', 'pages.dashboard.category-page')->name('category.view');
    Route::view('/customer', 'pages.dashboard.customer-page')->name('customer.view');
    Route::view('/product', 'pages.dashboard.product-page')->name('product.view');

    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
});
