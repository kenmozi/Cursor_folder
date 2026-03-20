<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug', 100)->unique();
            $table->string('acronym', 50)->nullable();
            $table->string('edition', 50)->nullable(); // e.g. "2025", "12th"
            $table->string('timezone', 50)->default('UTC');

            // Status: draft | active | archived
            $table->string('status', 20)->default('draft');

            // Review configuration
            $table->string('blind_mode', 10)->default('double'); // open|single|double
            $table->unsignedTinyInteger('min_reviewers')->default(2);
            $table->unsignedTinyInteger('max_reviewers')->default(3);
            $table->unsignedSmallInteger('max_pages')->nullable();

            // Key dates (stored in UTC)
            $table->timestamp('submission_open')->nullable();
            $table->timestamp('submission_close')->nullable();
            $table->timestamp('review_open')->nullable();
            $table->timestamp('review_close')->nullable();
            $table->timestamp('notification_date')->nullable();
            $table->timestamp('camera_ready_date')->nullable();  // renamed from camera_ready_deadline

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
