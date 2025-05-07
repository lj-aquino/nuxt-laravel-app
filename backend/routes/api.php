<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\AuthController;

// Public Auth Routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Make the encode endpoint public for now
Route::post('/encode', [FaceController::class, 'encode']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Existing Protected Routes
    Route::get('/check-student/{studentNumber}', [StudentController::class, 'checkStudent']);
    Route::post('/register-student', [StudentController::class, 'registerStudent']);
    // Remove this line since we moved it outside: Route::post('/encode', [FaceController::class, 'encode']);
});