<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->string('subject');
            $table->string('heading')->nullable();
            // Markdown with whitelisted {{ placeholders }} (escaped on render).
            $table->text('body');
            $table->string('action_label', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Outgoing email history. Recipient addresses are masked; credentials are never logged.
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 64)->nullable();
            $table->string('mailable');
            $table->string('recipient_masked');
            $table->string('subject')->nullable();
            $table->string('status', 20)->default('queued');
            $table->string('error', 500)->nullable();
            $table->nullableMorphs('related');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // Administrative audit trail and security events.
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 64);
            $table->nullableMorphs('auditable');
            $table->string('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('url', 2048)->nullable();
            $table->boolean('is_security_event')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->index(['event', 'created_at']);
            $table->index(['is_security_event', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
    }
};
