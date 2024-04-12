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
            $table->string('firstname')->nullable();
            $table->string('sirname')->nullable();
            $table->string('username')->nullable();
            $table->string('avatar')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('delete_reason')->nullable();
            $table->string('email_code')->nullable();
            $table->string('phone_code')->nullable();
            $table->timestamp('dob')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('phone_code_at')->nullable();
            $table->timestamp('email_code_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('password');
            $table->enum('gender', ['notset','male', 'female'])->nullable();
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
