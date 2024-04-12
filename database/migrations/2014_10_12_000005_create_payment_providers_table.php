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
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('service_desc')->nullable();
            $table->string('short_code')->nullable();
            $table->string('prod_token')->nullable();
            $table->string('dev_token')->nullable();
            $table->string('gateway_url')->nullable();
            $table->string('gateway_ip')->nullable();
            $table->string('contact')->nullable();
            $table->boolean('allowed')->default(false);
            $table->enum('status', ['dev', 'prod'])->default('dev');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
