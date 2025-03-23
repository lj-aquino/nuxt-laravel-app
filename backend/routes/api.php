<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/test', function () {
    return response()->json(['message' => 'Hello from Laravel API']);
});

// routes/api.php
Route::get('/check-student/{student_number}', [StudentController::class, 'checkStudentNumber']);

// routes/api.php
Route::post('/register-student', [StudentController::class, 'registerNewStudent']);
