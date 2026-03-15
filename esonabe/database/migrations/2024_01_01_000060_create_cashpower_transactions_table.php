<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cashpower_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_ref')->unique();
            $table->decimal('amount_cfa', 12, 2);
            $table->decimal('kwh_purchased', 10, 3);
            $table->decimal('balance_before_kwh', 10, 3);
            $table->decimal('balance_after_kwh', 10, 3);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method')->default('simulated');
            $table->string('token_code')->nullable(); // CashPower token
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashpower_transactions');
    }
};
