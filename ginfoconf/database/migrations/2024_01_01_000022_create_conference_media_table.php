<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Stores logo, cover image, and other conference media assets
        Schema::create('conference_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();

            // type: logo | cover | program
            $table->string('type', 30);

            // Storage disk identifier — 'local' by default, 's3' in production
            $table->string('disk', 20)->default('local');
            $table->string('path', 1000);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['conference_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_media');
    }
};
