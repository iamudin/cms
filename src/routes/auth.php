<?php
use Illuminate\Support\Facades\Route;
use Udiko\Cms\Http\Controllers\ApiController;
use Udiko\Cms\Http\Controllers\Auth\LoginController;
Route::prefix(admin_path())->controller(LoginController::class)->group(function ()  {
    Route::get('login', 'loginForm')->name('login');
    Route::get( 'captcha', 'generateCaptcha')->name('captcha');
    Route::post( 'login', 'loginSubmit')->name('login.submit');
    Route::match(['post', 'get'], 'logout',  'logout')->name('logout');
});
