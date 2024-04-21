<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//  Public Auth Routes
Route::controller(UserController::class)->prefix('users')->group(function () {
    Route::post('/store', 'store');
    Route::get('/{id}', 'show');
    Route::get('/', 'showAll')->name('users.get');
    Route::post('/update/{id}', 'update');
    // Route::delete('/delete/{id}', 'destroy');
    // Route::post('/store', 'store');
    // Route::post('/store', 'store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
    });
    Route::middleware('auth:api')->group(function () {
        Route::delete('/delete/{email}', [UserController::class, 'destroy']);
    });
});
