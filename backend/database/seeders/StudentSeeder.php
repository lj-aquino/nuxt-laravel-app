<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Adding sample names
        Student::create([
            'student_number' => '201909924',
            'name' => 'John Doe',  // Add a name value
        ]);

        Student::create([
            'student_number' => '202009958',
            'name' => 'Jane Smith',  // Add a name value
        ]);
    }
}
