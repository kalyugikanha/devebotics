<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Query desk: teams (departments) with members and routing rules, and the
 * team / topic / first-response columns on inquiries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable();
            $table->string('email')->nullable();
            $table->json('topics')->nullable();
            $table->boolean('auto_assign')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('member');
            $table->timestamp('last_assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('service_team', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();

            $table->primary(['service_id', 'team_id']);
        });

        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->string('topic', 30)->default('sales');
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('first_response_at')->nullable();

            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'status']);
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn(['topic', 'first_response_at']);
        });

        Schema::dropIfExists('service_team');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }
};
