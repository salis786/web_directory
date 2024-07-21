<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\WebsiteController;
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
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('websites', [WebsiteController::class, 'index']);
Route::get('categories', [CategoriesController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user', [AuthController::class, 'user']);

    Route::middleware('user')->group(function () {
        Route::post('websites', [WebsiteController::class, 'store']);
        Route::post('websites/{website}/vote', [WebsiteController::class, 'vote']);
    });

    Route::middleware('admin')->group(function () {
        Route::delete('websites/{website}', [WebsiteController::class, 'destroy']);
    });
});
