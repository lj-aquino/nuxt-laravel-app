<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceEncodingsTable extends Migration
{
    public function up()
    {
        Schema::create('face_encodings', function (Blueprint $table) {
            $table->id();  // Auto-incremented ID
            $table->string('student_number')->unique();  // References students table
            $table->json('encoding');  // Store encoding as JSON array
            $table->timestamps();  // created_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('face_encodings');
    }
}