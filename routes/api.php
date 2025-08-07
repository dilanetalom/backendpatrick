<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller\Api;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SiteController;

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
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register',[ApiAuthController::class, 'register']);




Route::middleware('auth:api')->group( function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::get('/users', [ApiAuthController::class, 'getUsers']);
    Route::get('/users/{id}', [ApiAuthController::class, 'getByIdUser']);
    Route::put('/users/{id}', [ApiAuthController::class, 'update']);
});
