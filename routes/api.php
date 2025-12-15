<?php

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
Route::get('/voitures', [VoitureController::class, 'index']);
Route::get('/voitures/{voiture}', [VoitureController::class, 'show']);
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/categories/{categorie}', [CategorieController::class, 'show']);
Route::get('/agences', [AgenceController::class, 'index']);
Route::get('/agences/{agence}', [AgenceController::class, 'show']);

// protection routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('clients', ClientController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('paiements', PaiementController::class);

    // Admin only routes (simplified for now, can be middleware restricted later)
    // la gestion des agences, categories, voitures
    Route::post('/agences', [AgenceController::class, 'store']);
    Route::put('/agences/{agence}', [AgenceController::class, 'update']);
    Route::delete('/agences/{agence}', [AgenceController::class, 'destroy']);

    Route::post('/categories', [CategorieController::class, 'store']);
    Route::put('/categories/{categorie}', [CategorieController::class, 'update']);
    Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy']);

    Route::post('/voitures', [VoitureController::class, 'store']);
    Route::put('/voitures/{voiture}', [VoitureController::class, 'update']);
    Route::delete('/voitures/{voiture}', [VoitureController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


