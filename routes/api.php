<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- VOS ROUTES PROTÉGÉES POUR N8N ---
Route::middleware('auth.apikey')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
});
