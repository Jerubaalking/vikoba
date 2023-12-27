<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('avatar')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('delete_reason')->nullable();
            $table->string('verification_code');
            $table->string('dob')->nullable();
            $table->string('email')->unique();
            $table->dateTime('last_login');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('gender', ['None','MALE', 'FEMALE'])->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
