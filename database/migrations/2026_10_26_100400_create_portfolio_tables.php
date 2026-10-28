<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            // Public label, e.g. "A national logistics provider" when anonymised.
            $table->string('client_label')->nullable();
            $table->boolean('is_client_anonymized')->default(true);
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('portfolio_category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('summary')->nullable();
            $table->longText('challenge')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('outcome')->nullable();
            // [{label, value, is_verified}] — only verified metrics are shown publicly.
            $table->json('metrics')->nullable();
            $table->string('duration')->nullable();
            $table->date('started_on')->nullable();
            $table->date('completed_on')->nullable();
            $table->boolean('show_timeline')->default(false);
            $table->string('project_url', 2048)->nullable();
            $table->foreignId('hero_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_demo')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        Schema::create('portfolio_service', function (Blueprint $table) {
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();

            $table->primary(['portfolio_id', 'service_id']);
        });

        Schema::create('portfolio_product', function (Blueprint $table) {
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->primary(['portfolio_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_product');
        Schema::dropIfExists('portfolio_service');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('portfolio_categories');
    }
};
