<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();  // Auto-incremented ID
            $table->string('student_number')->unique();  // Unique student number
            $table->string('name');  // Student's name
            $table->boolean('enrolled')->default(true);  // Enrolled status
            $table->boolean('id_validated')->default(false);  // ID validated status
            $table->foreignId('face_encoding_id')->nullable()->constrained('face_encodings');  // Foreign key reference to face_encodings
            $table->timestamps();  // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
