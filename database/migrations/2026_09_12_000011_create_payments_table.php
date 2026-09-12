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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('connection_request_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('provider')->default('paystack');
            $table->unsignedBigInteger('amount'); // in kobo
            $table->string('currency', 3)->default('NGN');
            $table->string('status')->default('pending'); // pending, successful, failed, cancelled, refunded
            $table->string('provider_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
