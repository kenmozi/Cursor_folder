<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();

            // user_id is nullable because committee members can be listed without a platform account
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Denormalized fields allow listing external members who have no account
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('affiliation', 500)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('photo_path', 500)->nullable();

            // role: chair | co_chair | pc_member | organizing | technical | sponsor
            $table->string('role', 30)->default('pc_member');

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['conference_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
