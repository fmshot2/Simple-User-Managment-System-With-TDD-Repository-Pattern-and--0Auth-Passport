<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

//  Public Auth Routes
Route::controller(UserController::class)->prefix('users')->group(function () {
    Route::post('/store', 'store');
    Route::get('/{id}', 'show');
    Route::get('/', 'showAll')->name('users.get');
    Route::post('/update/{id}', 'update');
    Route::middleware('admin')->group(function () {
        Route::delete('/delete/{email}', [UserController::class, 'destroy']);
    });
})->middleware('auth:api');
