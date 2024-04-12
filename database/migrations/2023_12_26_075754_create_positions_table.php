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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('duties');
            $table->string('description');
            $table->boolean('voted')->default(false);
            $table->boolean('open')->default(false);
            $table->timestamp('dov')->nullableTimestamps();
            $table->timestamp('dos')->nullableTimestamps();
            $table->timestamp('doe')->nullableTimestamps();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
