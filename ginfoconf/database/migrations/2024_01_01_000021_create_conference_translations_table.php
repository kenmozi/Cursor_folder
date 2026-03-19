<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conference_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);  // en | fr | ja | ...

            $table->string('title', 500);
            $table->string('subtitle', 500)->nullable();
            $table->text('description')->nullable();   // overview / about section
            $table->longText('cfp_text')->nullable();  // call for papers (rich HTML)
            $table->text('venue_text')->nullable();
            $table->text('contact_text')->nullable();

            $table->timestamps();

            $table->unique(['conference_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_translations');
    }
};
