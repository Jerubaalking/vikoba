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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['Placed', 'Paying', 'Paid'])->default('placed');
            $table->boolean('has_installments')->default(false);
            $table->enum('type', ['Installment', 'Fixed'])->default('Fixed');
            $table->string('code')->nullable();
            $table->double('amount_expected', null, 2)->default(0);
            $table->string('description');
            $table->string('ip_address');
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
        Schema::dropIfExists('orders');
    }
};
