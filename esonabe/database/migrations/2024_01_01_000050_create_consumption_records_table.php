<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consumption_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_date');
            $table->decimal('kwh_consumed', 10, 3);
            $table->decimal('meter_reading', 12, 3);
            $table->string('source')->default('manual'); // manual, smart, estimated
            $table->timestamps();

            $table->unique(['meter_id', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_records');
    }
};
