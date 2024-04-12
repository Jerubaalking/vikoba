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
        Schema::create('vikobas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('registration')->nullable();
            $table->integer('kyc_limit')->nullable();
            $table->integer('max_interest_percent')->nullable();
            $table->string('max_members')->nullable();
            $table->string('form_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('terms_conditions_url')->nullable();
            $table->string('code');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vikobas');
    }
};
