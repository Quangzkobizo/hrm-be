<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index')->name('users.index');
    Route::put('users/{id}', 'update')->name('users.update');
    Route::post('users-avatar/{id}', 'updateAvatar')->name('users.update-avatar');
    Route::get('users/me', 'me')->name('users.me');
    Route::get('users/{id}', 'show')->name('users.show');
    Route::delete('users/{id}', 'destroy');
});

Route::prefix('projects')->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        Route::get('/', 'index')->name('projects.index');
        Route::post('/', 'store')->name('projects.store');
        Route::get('/{id}', 'show')->name('projects.show');
        Route::put('/{id}', 'update')->name('projects.update');
        Route::delete('/{id}', 'destroy')->name('projects.destroy');
    });
});
