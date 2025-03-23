<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();  // Auto-incremented ID
            $table->string('username')->unique();  // Unique username
            $table->text('password_hash');  // Hashed password
            $table->string('role');  // Role (e.g., security_admin)
            $table->timestamps();  // created_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
