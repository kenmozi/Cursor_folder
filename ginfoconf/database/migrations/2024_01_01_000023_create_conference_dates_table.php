<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Human-readable important dates shown on the public conference page
        Schema::create('conference_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['conference_id', 'sort_order']);
        });

        Schema::create('conference_date_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_date_id')
                  ->constrained('conference_dates')
                  ->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('label', 300); // e.g. "Paper submission deadline"

            $table->unique(['conference_date_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_date_translations');
        Schema::dropIfExists('conference_dates');
    }
};
