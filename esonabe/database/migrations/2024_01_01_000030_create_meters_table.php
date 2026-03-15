<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('meter_number')->unique();
            $table->string('serial_number')->nullable();
            $table->enum('type', ['NORMAL', 'CASHPOWER'])->default('NORMAL');
            $table->enum('meter_class', ['A', 'B1', 'B2', 'C1', 'C2'])->default('B1');
            $table->integer('amperage')->default(5);
            $table->decimal('cashpower_balance_kwh', 10, 3)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_reading_at')->nullable();
            $table->decimal('last_reading_kwh', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
