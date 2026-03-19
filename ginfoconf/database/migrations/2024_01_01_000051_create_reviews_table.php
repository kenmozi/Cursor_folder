<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // For MVP: simplified review form (score + recommendation + two comment fields).
        // Dynamic review criteria (review_forms / review_criteria) are post-MVP.
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // One review per assignment (1:1)
            $table->foreignId('assignment_id')->unique()->constrained('review_assignments')->cascadeOnDelete();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // status: draft | submitted
            $table->string('status', 20)->default('draft');

            // Overall score: 1–10
            $table->unsignedTinyInteger('overall_score')->nullable();

            // recommendation: strong_accept | accept | weak_accept | borderline |
            //                 weak_reject | reject | strong_reject
            $table->string('recommendation', 30)->nullable();

            // Visible to authors (after decision or when chair releases)
            $table->longText('comments_to_authors')->nullable();

            // Visible only to chair and PC members
            $table->longText('comments_to_chair')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'status']);
            $table->index('reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
