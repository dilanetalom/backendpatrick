<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller\Api;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProjectController;


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
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    // Négociation de prix (client/admin)
    Route::post('/proposals/{project}', [ProjectController::class, 'proposePrice']);
    Route::patch('/projects/{project}/update-progress', [ProjectController::class, 'updateProgress']);
    Route::post('/projects/{project}/validate', [ProjectController::class, 'validateProject']);
    Route::post('/projects/{project}/contract', [ProjectController::class, 'uploadContract']);
    Route::patch('/projects/{project}/complete', [ProjectController::class, 'completeProject']);
});
