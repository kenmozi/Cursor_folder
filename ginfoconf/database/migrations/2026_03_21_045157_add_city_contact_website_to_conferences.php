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
        Schema::table('conferences', function (Blueprint $table) {
            $table->string('website_url', 500)->nullable()->after('status');
            $table->string('location', 500)->nullable()->after('website_url');
            $table->string('city', 200)->nullable()->after('location');
            $table->string('contact_name', 255)->nullable()->after('city');
            $table->string('contact_email', 255)->nullable()->after('contact_name');
            $table->string('contact_phone', 100)->nullable()->after('contact_email');
            $table->string('contact_address', 1000)->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn([
                'website_url', 'location', 'city',
                'contact_name', 'contact_email', 'contact_phone', 'contact_address',
            ]);
        });
    }
};
