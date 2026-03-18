<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliation')->nullable()->after('name');
            $table->char('country', 2)->nullable()->after('affiliation');
            $table->text('bio')->nullable()->after('country');
            $table->string('locale', 10)->default('en')->after('bio');
            $table->boolean('is_super_admin')->default(false)->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['affiliation', 'country', 'bio', 'locale', 'is_super_admin']);
        });
    }
};
