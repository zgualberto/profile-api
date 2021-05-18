<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/confirm', [App\Http\Controllers\AuthController::class, 'confirm'])->name('auth.confirm');
        Route::post('/invite', [App\Http\Controllers\AuthController::class, 'invite'])->name('auth.invite');
        Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('auth.profile');
    });
});


