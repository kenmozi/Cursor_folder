<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submission_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();

            // Nullable: external co-authors may not have a platform account
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Denormalized for display and blind review (name hidden from reviewers in double-blind)
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('affiliation', 500)->nullable();
            $table->char('country', 2)->nullable();

            $table->boolean('is_corresponding')->default(false);
            $table->boolean('is_presenter')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->index(['submission_id', 'sort_order']);
        });

        Schema::create('submission_topics', function (Blueprint $table) {
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->primary(['submission_id', 'topic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_topics');
        Schema::dropIfExists('submission_authors');
    }
};
