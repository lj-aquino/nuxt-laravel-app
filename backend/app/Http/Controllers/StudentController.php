<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\Log;
use App\Services\ApiKeyRequestService;

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

    // Check via external AMIS API
    public function isStudent(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string'
        ]);

        $studentNumber = $request->input('student_number');

        try {
            $endpoint = env('AMIS_API_URL') . '/is-student';
            $apiKeyRequestService = new ApiKeyRequestService();
            $encrypted_api_key = $apiKeyRequestService->generateEncryptedApiKey();

            $response = Http::withHeaders([
                'X-Api-Key' => $encrypted_api_key,
            ])->get($endpoint, [
                'student_number' => $studentNumber,
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check student status',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
