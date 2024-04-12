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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code');
            $table->boolean('accepted_membership')->default(false);
            $table->boolean('accepted_terms_conditions')->default(false);
            $table->double('credit_score', 5, 2)->default(20);
            $table->double('kyc_meter', 5, 2)->default(10)->comment("kyc meter 1 month equals 10 points"); // one month has 10 score for 3 month -30 score
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('vikoba_id')->constrained('vikobas')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
