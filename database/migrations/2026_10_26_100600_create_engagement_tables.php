<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            // "admin" (entered by staff from an approved source) or "client_form".
            $table->string('source', 20)->default('client_form');
            $table->string('client_name', 120);
            $table->string('company_name', 160)->nullable();
            $table->string('job_title', 120)->nullable();
            // Encrypted at rest; never rendered publicly. Hash enables duplicate checks.
            $table->text('email')->nullable();
            $table->string('email_hash', 64)->nullable()->index();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_name', 160)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('rating_communication')->nullable();
            $table->unsignedTinyInteger('rating_quality')->nullable();
            $table->unsignedTinyInteger('rating_delivery')->nullable();
            $table->text('feedback');
            $table->boolean('publish_permission')->default(false);
            $table->boolean('display_identity_consent')->default(false);
            // Submitted photo/logo stays on the private disk until approved.
            $table->string('submitted_photo_path')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->text('moderation_note')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('content_hash', 64)->nullable()->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured', 'sort_order']);
        });

        // Auditable consent trail (privacy notice, testimonial publishing, cookies, ...).
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('consentable');
            $table->string('consent_type', 40);
            $table->boolean('granted');
            $table->string('policy_version', 32)->nullable();
            $table->json('details')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['consent_type', 'created_at']);
        });

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('type', 20)->default('quick');
            $table->string('name', 120);
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('company', 160)->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('budget_range', 60)->nullable();
            $table->string('timeline', 60)->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('status', 20)->default('new');
            $table->string('priority', 20)->default('normal');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->json('utm')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('content_hash', 64)->nullable()->index();
            $table->boolean('is_spam')->default(false);
            $table->foreignId('duplicate_of_id')->nullable()->constrained('contact_inquiries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index('email');
        });

        Schema::create('inquiry_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_inquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30);
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['contact_inquiry_id', 'created_at']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name', 120)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('source', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('inquiry_activities');
        Schema::dropIfExists('contact_inquiries');
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('testimonials');
    }
};
