<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Log;

class StudentController extends Controller
{
    // Check if student exists
    public function checkStudent($studentNumber)
    {
        try {
            $student = Student::where('student_number', $studentNumber)->first();

            if ($student) {
                // Determine enrolled status
                $remarks = $student->enrolled ? 'Enrolled' : 'Not enrolled';
                return response()->json(['exists' => true, 'remarks' => $remarks]);
            } else {
                // If student doesn't exist, return false
                return response()->json(['exists' => false]);
            }
        } catch (\Exception $e) {
            // Return error message in case of failure
            return response()->json(['error' => 'An error occurred while checking the student. Please try again.'], 500);
        }
    }

    // Register the student if not already registered
    public function registerStudent(Request $request)
    {
        $studentNumber = $request->input('student_number');
        $student = Student::where('student_number', $studentNumber)->first();

        if (!$student) {
            // Register new student
            $student = new Student();
            $student->student_number = $studentNumber;
            $student->enrolled = false; // Default value
            $student->save();

            // Log the new user
            Log::create([
                'student_number' => $studentNumber,
                'has_id' => false,  // Change this based on validation
                'entry_time' => now(),
                'remarks' => 'New user',
            ]);

            return response()->json(['exists' => true, 'remarks' => 'New user']);
        } else {
            // If student already exists, return remarks (Enrolled or Not enrolled)
            $remarks = $student->enrolled ? 'Enrolled' : 'Not enrolled';
            return response()->json(['exists' => true, 'remarks' => $remarks]);
        }
    }
}
