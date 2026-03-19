<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviewer_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();

            // Null until the invitee creates/links their account
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('email', 255);

            // Cryptographically random 64-char token — single use
            $table->string('token', 64)->unique();

            // status: pending | accepted | declined | expired
            $table->string('status', 20)->default('pending');

            $table->text('message')->nullable(); // custom message from chair
            $table->string('decline_reason', 500)->nullable();

            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['conference_id', 'status']);
            $table->index(['email', 'conference_id']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviewer_invitations');
    }
};
