<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('website_url', 2048)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
            // Public logo display requires a recorded approval from the client.
            $table->boolean('logo_usage_approved')->default(false);
            $table->timestamp('logo_approved_at')->nullable();
            $table->string('approval_reference')->nullable();
            $table->boolean('show_on_home')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 30)->default('technology');
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('website_url', 2048)->nullable();
            $table->text('description')->nullable();
            $table->boolean('logo_usage_approved')->default(false);
            $table->timestamp('logo_approved_at')->nullable();
            $table->string('approval_reference')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('job_title');
            $table->string('department')->nullable();
            $table->text('bio')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('linkedin_url', 2048)->nullable();
            $table->string('github_url', 2048)->nullable();
            $table->string('x_url', 2048)->nullable();
            $table->boolean('is_leadership')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->string('year', 16);
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        // Certifications, awards and memberships. Only verified rows are published.
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->default('certification');
            $table->string('title');
            $table->string('issuer')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('verification_url', 2048)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        // Company statistics shown as animated counters. Must be verified to display.
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('value', 14, 2);
            $table->unsignedTinyInteger('decimals')->default(0);
            $table->string('prefix', 8)->nullable();
            $table->string('suffix', 8)->nullable();
            $table->string('description')->nullable();
            $table->string('icon', 64)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->boolean('show_on_home')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Attach FAQs to services, products, industries, the contact page, ...
        Schema::create('faqables', function (Blueprint $table) {
            $table->foreignId('faq_id')->constrained()->cascadeOnDelete();
            $table->morphs('faqable');
            $table->unsignedInteger('sort_order')->default(0);

            $table->primary(['faq_id', 'faqable_type', 'faqable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqables');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
        Schema::dropIfExists('statistics');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('clients');
    }
};
