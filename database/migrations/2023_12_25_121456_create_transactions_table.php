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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('redeem_code')->nullable();
            $table->string('receipt')->nullable();
            $table->datetime('payment_date')->nullable();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->text('comment')->nullable();
            $table->double('amount', 9, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
