<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubpageController;
use App\Http\Controllers\ConferenceYearController;
use App\Http\Controllers\UserController;

// Admin-only endpoints
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // CRUD pre používateľov
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Priraďovanie editorov k ročníku
    Route::post('/years/{conferenceYear}/editors', [ConferenceYearController::class, 'assignEditor']);
    Route::delete('/years/{conferenceYear}/editors/{user}', [ConferenceYearController::class, 'removeEditor']);

    // CRUD pre ročníky
    Route::post('/years', [ConferenceYearController::class, 'store']);
    Route::put('/years/{conferenceYear}', [ConferenceYearController::class, 'update']);
    Route::delete('/years/{conferenceYear}', [ConferenceYearController::class, 'destroy']);
});

// Editor aj admin môžu spravovať podstránky
Route::middleware(['auth:sanctum', 'role:editor,admin'])->group(function () {
    Route::post('/subpages', [SubpageController::class, 'store']);
    Route::put('/subpages/{subpage}', [SubpageController::class, 'update']);
    Route::delete('/subpages/{subpage}', [SubpageController::class, 'destroy']);
});

// Verejné endpointy (GET pre roky a podstránky)
Route::get('/years', [ConferenceYearController::class, 'index']);
Route::get('/years/{conferenceYear}', [ConferenceYearController::class, 'show']);
Route::get('/subpages', [SubpageController::class, 'index']);
Route::get('/subpages/{subpage}', [SubpageController::class, 'show']);

// Authenticated user info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Login & Logout
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');