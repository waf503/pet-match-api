<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\FeedController;
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
});

require __DIR__.'/auth.php';
