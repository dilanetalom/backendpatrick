<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\ConversationController;

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
    Route::get('/projects', [ProjectController::class, 'index']); // Affiche les projets du client ou tous pour l'admin
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    // Actions sur les projets (nécessitent un middleware de rôle)
    Route::post('/projects/{project}/accept-proposal', [ProjectController::class, 'acceptProposal']); // Admin accepte
    Route::post('/projects/{project}/negotiate', [ProjectController::class, 'refuseAndNegotiate']); // Admin refuse et négocie
    Route::put('/projects/{project}/update-price', [ProjectController::class, 'updateClientPrice']); // Client modifie le prix

    Route::patch('/projects/{project}/update-progress', [ProjectController::class, 'updateProgress']); // Admin met à jour la progression

    // Contrats et signatures
    Route::post('/projects/{project}/generate-contract', [ProjectController::class, 'generateContract']); // Client génère le contrat
    Route::post('/projects/{project}/sign-contract', [ProjectController::class, 'signContract']); // Les deux signent

    // Paiements
    Route::post('/projects/{project}/upload-proof', [PaymentController::class, 'uploadProof']); // Client télécharge la preuve
    Route::post('/projects/{project}/verify-payment', [PaymentController::class, 'verify']); // Admin vérifie

    // Chat
    Route::get('/projects/{project}/conversation', [ConversationController::class, 'getMessages']);
    Route::post('/projects/{project}/send-message', [ConversationController::class, 'sendMessage']);
});
