<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller\Api;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\PresenceController;

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
Route::post('/test',[PresenceController::class, 'test']);
Route::post('/presence',[PresenceController::class, 'create']);
Route::get('/getpresence',[PresenceController::class, 'index']);

Route::middleware('auth:api')->group( function () {
Route::get('/show/{id}',[PresenceController::class, 'show']);
Route::get('/user_presence',[ApiAuthController::class, 'user']);
Route::get('/allpresence',[PresenceController::class, 'all']);
Route::get('/user/{id}',[ApiAuthController::class, 'getbyiduser']);
Route::get('/users',[ApiAuthController::class, 'getUser']);
Route::put('/update/{id}',[ApiAuthController::class, 'update']);
Route::post('/logout',[ApiAuthController::class, 'logout']);
});
