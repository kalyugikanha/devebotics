<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional illustration key (App\Support\Art\IllustrationRegistry) used when no photo is uploaded.
     */
    public function up(): void
    {
        foreach (['services', 'industries', 'products', 'portfolios'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('illustration', 40)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['services', 'industries', 'products', 'portfolios'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('illustration');
            });
        }
    }
};
