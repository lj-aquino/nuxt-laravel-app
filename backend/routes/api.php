<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// Check if student exists
Route::get('/check-student/{studentNumber}', [StudentController::class, 'checkStudent']);

// Register student
Route::post('/register-student', [StudentController::class, 'registerStudent']);
