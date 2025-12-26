<?php

use App\Http\Controllers\Api\VoitureImageController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AgenceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\VoitureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes publiques pour la consultation (index et show)
Route::apiResource('voitures', VoitureController::class)->only(['index', 'show']);
Route::apiResource('categories', CategorieController::class)->only(['index', 'show']);
Route::apiResource('agences', AgenceController::class)->only(['index', 'show']);

// protection routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', fn (Request $request) => $request->user());

    Route::apiResource('clients', ClientController::class);
    Route::post('/clients/{id}/photo', [ClientController::class, 'updatePhoto']);
    Route::apiResource('admins', AdminController::class);
    Route::post('/admins/{id}/photo', [AdminController::class, 'updatePhoto']);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('paiements', PaiementController::class);

    // Routes d'administration (création, mise à jour, suppression)
    Route::post('voitures/{voiture}', [VoitureController::class, 'update']); // Support pour FormData (multipart/form-data)
    Route::apiResource('voitures', VoitureController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategorieController::class)->except(['index', 'show']);
    Route::apiResource('agences', AgenceController::class)->except(['index', 'show']);

    // Galerie d'images des voitures
    Route::get('/voitures/{voiture}/images', [VoitureImageController::class, 'index']);
    Route::post('/voitures/{voiture}/images', [VoitureImageController::class, 'store']);
    Route::delete('/voitures/images/{image}', [VoitureImageController::class, 'destroy']);
});
