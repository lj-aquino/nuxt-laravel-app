<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FaceController;

// Check if student exists
Route::get('/check-student/{studentNumber}', [StudentController::class, 'checkStudent']);

// Register student
Route::post('/register-student', [StudentController::class, 'registerStudent']);

Route::post('/encode', [FaceController::class, 'encode']);

Route::post('/check-student', [StudentController::class, 'isStudent']);
