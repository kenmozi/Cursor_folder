<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->foreignId('submitter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title', 500);
            $table->text('abstract');

            // Comma-separated or JSON keywords — JSON used here for easy querying
            $table->json('keywords')->nullable();

            // status state machine (see SubmissionStatus enum)
            $table->string('status', 30)->default('draft')->index();

            // Internal note written by chair on accept/reject
            $table->text('decision_note')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->index(['conference_id', 'status']);
            $table->index(['submitter_id', 'conference_id']);

            // Full-text index on title + abstract (MySQL FULLTEXT, ignored by SQLite)
            // Postgres users: add tsvector index via raw SQL in a separate migration
            $table->fullText(['title', 'abstract'])->algorithm('ngram');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
