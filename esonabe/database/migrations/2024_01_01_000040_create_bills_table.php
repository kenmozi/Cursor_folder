<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');
            $table->decimal('consumption_kwh', 10, 3);
            $table->decimal('amount_cfa', 12, 2);
            $table->decimal('taxes_cfa', 10, 2)->default(0);
            $table->decimal('total_cfa', 12, 2);
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('reading_start', 12, 3);
            $table->decimal('reading_end', 12, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
