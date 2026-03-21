<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conference_translations', function (Blueprint $table) {
            $table->longText('publication_guidelines')->nullable()->after('contact_text');
        });
    }

    public function down(): void
    {
        Schema::table('conference_translations', function (Blueprint $table) {
            $table->dropColumn('publication_guidelines');
        });
    }
};
