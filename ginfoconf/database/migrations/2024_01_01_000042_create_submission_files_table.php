<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            $table->unsignedSmallInteger('version')->default(1);

            // type: manuscript | supplementary | camera_ready
            $table->string('type', 30);

            $table->string('disk', 20)->default('local');
            $table->string('path', 1000);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Only one active manuscript per submission at any time
            $table->boolean('is_active')->default(true);

            $table->timestamp('uploaded_at')->useCurrent();

            $table->index(['submission_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
