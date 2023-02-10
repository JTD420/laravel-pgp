<?php

use App\Http\Controllers\PGPController;
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
    Route::get('/message', [PGPController::class, 'index'])->name('messages.index');
    Route::get('/PGP', function () {
        return view('PGP::welcome');
    });
});
