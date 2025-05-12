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

Route::get('/test-amis/{studentNumber}', function($studentNumber) {
    try {
        Log::info('Testing AMIS API for student: ' . $studentNumber);
        
        $controller = new \App\Http\Controllers\StudentController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['student_number' => $studentNumber]);
        
        $response = $controller->isStudent($request);
        
        return response()->json([
            'test_result' => $response->getData(),
            'log_message' => 'Check Laravel logs for detailed information'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Add this to routes/api.php for testing
Route::get('/test-amis-manual/{studentNumber}', function($studentNumber) {
    try {
        Log::info('Manual AMIS Test - Student: ' . $studentNumber);
        
        // Test different endpoints and methods
        $endpoints = [
            'https://api.amis.uplb.edu.ph/api/is-student',
            'https://api.amis.uplb.edu.ph/is-student',  // Without /api
            'https://api.amis.uplb.edu.ph/api/student/check',  // Alternative path
        ];
        
        $results = [];
        
        foreach ($endpoints as $endpoint) {
            Log::info('Testing endpoint: ' . $endpoint);
            
            try {
                $response = Http::withOptions(['verify' => false, 'timeout' => 10])
                    ->get($endpoint);
                
                $results[] = [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body()
                ];
            } catch (Exception $e) {
                $results[] = [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json($results);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});