<?php

use App\Http\Controllers\PGP\AuthenticationController;
use App\Http\Controllers\PGP\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Laravel-PGP Routes
|--------------------------------------------------------------------------
|
| This file includes routes for the Laravel-PGP package, offering
| PGP encryption/decryption in Laravel for secure communication.
| Customizable chat interface included.
|
*/

$prefix = config('PGP.prefix');
Route::prefix("$prefix")->group(function () {
    $prefix = config('PGP.prefix');
    Route::get('/inbox', [MessageController::class, 'index'])->name("$prefix.conversations.index");
    Route::get('/messages', [MessageController::class, 'index'])->name("$prefix.messages.index");
    Route::post('/messages/{id}/decrypt', [MessageController::class, 'decrypt'])->name("$prefix.messages.decrypt");
    Route::post('/messages/{id}/reply', [MessageController::class, 'reply'])->name("$prefix.messages.reply");
    Route::get('/messages/send', [MessageController::class, 'encrypt'])->name("$prefix.messages.send");
    Route::match(['get', 'post'], '/messages/{id}/thread', [MessageController::class, 'show'])->name('viewThread');


});
if (config('PGP.uses_custom_auth')) {
    // Adds the login route without prefix if not already defined to avoid errors when redirecting users on session expiration.
    if (!Route::has('login')) {
        Route::get('/login', [AuthenticationController::class, 'login'])->name('login');
    }
    Route::prefix("$prefix")->group(function () {
        $prefix = config('PGP.prefix');
        Route::get('/login', [AuthenticationController::class, 'login'])->name("$prefix.login");
        Route::post('/login', [AuthenticationController::class, 'authenticate'])->name("$prefix.authenticate");
        Route::get('/signup', [AuthenticationController::class, 'create'])->name("$prefix.signup");
        Route::post('/signup', [AuthenticationController::class, 'store'])->name("$prefix.signup.store");
        Route::get('/logout', [AuthenticationController::class, 'logout'])->name("$prefix.logout");
    });
}
