<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();  // Auto-incremented ID
            $table->string('student_number');  // Student number for log entry
            $table->boolean('has_id')->default(false);  // Whether the student had an ID
            $table->timestamp('entry_time');  // Timestamp of entry attempt
            $table->string('status');  // Status (Verified, Failed, etc.)
            $table->text('remarks')->nullable();  // Remarks
            $table->timestamps();  // created_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
