<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchProposalController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserLocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Catálogo público — no requiere autenticación
Route::get('/catalog/species', [CatalogController::class, 'species']);
Route::get('/catalog/breeds',  [CatalogController::class, 'breeds']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/user/location', [UserLocationController::class, 'update']);
    Route::apiResource('pets', PetController::class);
    Route::delete('/pets/{pet}/photos/{photo}', [PetController::class, 'destroyPhoto']);
    Route::post('/pets/{pet}/like', [PetController::class, 'toggleLike']);
    Route::get('/feed', [FeedController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']); // POST con _method PATCH para multipart

    // Match proposals
    Route::get('/match-proposals', [MatchProposalController::class, 'index']);
    Route::post('/match-proposals', [MatchProposalController::class, 'store']);
    Route::patch('/match-proposals/{proposal}', [MatchProposalController::class, 'update']);

    // Matches
    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{match}', [MatchController::class, 'show']);
    Route::patch('/matches/{match}', [MatchController::class, 'update']);

    // Messages
    Route::get('/matches/{match}/messages', [MessageController::class, 'index']);
    Route::post('/matches/{match}/messages', [MessageController::class, 'store']);

    // Notifications
    Route::get('/notifications/summary', [NotificationController::class, 'summary']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll']);
    Route::patch('/notifications/{notification}', [NotificationController::class, 'markRead']);
});

require __DIR__.'/auth.php';
