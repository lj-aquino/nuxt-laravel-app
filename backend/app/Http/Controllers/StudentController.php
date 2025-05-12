<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\Log;
use App\Services\ApiKeyRequestService;
use Illuminate\Support\Facades\Log as LaravelLog;

class StudentController extends Controller
{
    // Local check if student exists in DB
    public function checkStudent($studentNumber)
    {
        try {
            $student = Student::where('student_number', $studentNumber)->first();

            if ($student) {
                $remarks = $student->enrolled ? 'Enrolled' : 'Not enrolled';
                return response()->json(['exists' => true, 'remarks' => $remarks]);
            } else {
                return response()->json(['exists' => false]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while checking the student. Please try again.'], 500);
        }
    }

    // Register student locally if not already registered
    public function registerStudent(Request $request)
    {
        $studentNumber = $request->input('student_number');
        $student = Student::where('student_number', $studentNumber)->first();

        if (!$student) {
            $student = new Student();
            $student->student_number = $studentNumber;
            $student->enrolled = false;
            $student->save();

            Log::create([
                'student_number' => $studentNumber,
                'has_id' => false,
                'entry_time' => now(),
                'remarks' => 'New user',
            ]);

            return response()->json(['exists' => true, 'remarks' => 'New user']);
        } else {
            $remarks = $student->enrolled ? 'Enrolled' : 'Not enrolled';
            return response()->json(['exists' => true, 'remarks' => $remarks]);
        }
    }

    public function isStudent(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string'
        ]);

        $studentNumber = str_replace('-', '', $request->input('student_number'));
        LaravelLog::info('AMIS Request - Checking student: ' . $studentNumber);

        try {
            // Check if AMIS API key is configured
            $apiKey = config('app.api_key');
            if (!$apiKey || $apiKey === '<TO_BE_PROVIDED_BY_AMIS_TEAM>') {
                LaravelLog::warning('AMIS API - API key not configured');
                return response()->json([
                    'is_student' => false,
                    'message' => 'AMIS API key not configured'
                ]);
            }

            // Use config as shown in instructions
            $endpoint = config('app.amis_api_url') . '/is-student';
            LaravelLog::info('AMIS API - Endpoint: ' . $endpoint);
            
            $apiKeyRequestService = new ApiKeyRequestService();
            $encrypted_api_key = $apiKeyRequestService->generateEncryptedApiKey();

            // IMPORTANT: For local development, disable SSL verification
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for local development
                'timeout' => 30,   // Set timeout
            ])->withHeaders([
                'X-Api-Key' => $encrypted_api_key,
            ])->get($endpoint, [
                'student_number' => $studentNumber,
            ]);

            LaravelLog::info('AMIS API - Response status: ' . $response->status());
            LaravelLog::info('AMIS API - Response body: ' . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                LaravelLog::info('AMIS API - Success response: ' . json_encode($responseData));
                return response()->json($responseData);
            } else {
                LaravelLog::error('AMIS API - Request failed with status: ' . $response->status());
                return response()->json([
                    'is_student' => false,
                    'error' => 'AMIS API request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            LaravelLog::error('AMIS API - Exception: ' . $e->getMessage());
            return response()->json([
                'is_student' => false,
                'error' => 'AMIS API error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
