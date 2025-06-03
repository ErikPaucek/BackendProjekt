<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubpageController;
use App\Http\Controllers\ConferenceYearController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/years/{conferenceYear}/editors', [ConferenceYearController::class, 'assignEditor']);
    Route::delete('/years/{conferenceYear}/editors/{user}', [ConferenceYearController::class, 'removeEditor']);

    Route::post('/years', [ConferenceYearController::class, 'store']);
    Route::put('/years/{conferenceYear}', [ConferenceYearController::class, 'update']);
    Route::delete('/years/{conferenceYear}', [ConferenceYearController::class, 'destroy']);

    Route::get('/dashboard-data', function () {
        return response()->json(['message' => 'Admin dashboard data']);
    });
});

Route::middleware(['auth:sanctum', 'role:editor'])->group(function () {
    Route::get('/editordashboard-data', function () {
        return response()->json(['message' => 'Editor dashboard data']);
    });
});

Route::middleware(['auth:sanctum', 'role:editor,admin'])->group(function () {
    Route::post('/subpages', [SubpageController::class, 'store']);
    Route::put('/subpages/{subpage}', [SubpageController::class, 'update']);
    Route::delete('/subpages/{subpage}', [SubpageController::class, 'destroy']);
    Route::post('/upload-image', [UploadController::class, 'upload']);
});

Route::get('/years', [ConferenceYearController::class, 'index']);
Route::get('/years/{conferenceYear}', [ConferenceYearController::class, 'show']);
Route::get('/subpages', [SubpageController::class, 'index']);


Route::get('/subpages/slug/{year}/{slug}', [SubpageController::class, 'showBySlugAndYear']);
Route::get('/subpages/{subpage}', [SubpageController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');