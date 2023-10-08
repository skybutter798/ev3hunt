<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;  // Import the AuthController

Route::get('/', [GameController::class, 'index']);

Route::get('/game', [GameController::class, 'index']);
Route::post('/checkGrid', [GameController::class, 'checkGrid']);

// Use the array syntax for AuthController routes
Route::get('login/twitter', [AuthController::class, 'redirectToTwitter'])->name('login.twitter');
Route::get('login/twitter/callback', [AuthController::class, 'handleTwitterCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/wallet', [GameController::class, 'saveWalletAddress'])->middleware('auth');