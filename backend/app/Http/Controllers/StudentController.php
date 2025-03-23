<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Add the Log facade

class StudentController extends Controller
{
    public function checkStudentNumber($student_number)
    {
        Log::info("Checking if student exists: $student_number");

        $student = Student::where('student_number', $student_number)->first();

        if ($student) {
            Log::info("Student found: $student_number");
        } else {
            Log::info("Student not found: $student_number");
        }

        return response()->json([
            'exists' => $student ? true : false,
        ]);
    }

    public function registerNewStudent(Request $request)
    {
        // Log the incoming request data
        Log::info('Registering new student', ['student_number' => $request->student_number]);

        $validated = $request->validate([
            'student_number' => 'required|string|unique:students,student_number',
        ]);

        // Assigning a fake name for now
        $student = new Student();
        $student->student_number = $validated['student_number'];
        $student->name = "Student " . $validated['student_number'];  // Fake name generation
        $student->save();

        Log::info("New student registered: " . $student->student_number);

        return response()->json(['message' => 'Student registered successfully.']);
    }
}
