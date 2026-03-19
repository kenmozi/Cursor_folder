<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tracks group topics (optional — conferences may have a single default track)
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['conference_id', 'slug']);
        });

        Schema::create('track_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name', 300);
            $table->text('description')->nullable();

            $table->unique(['track_id', 'locale']);
        });

        // Topics belong to a track (or directly to a conference if no track)
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['conference_id', 'track_id']);
        });

        Schema::create('topic_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name', 300);

            $table->unique(['topic_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topic_translations');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('track_translations');
        Schema::dropIfExists('tracks');
    }
};
