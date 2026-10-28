<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrations_roll_back_and_re_run_cleanly(): void
    {
        $this->assertTrue(Schema::hasTable('contact_inquiries'));

        Artisan::call('migrate:rollback', ['--force' => true]);
        $this->assertFalse(Schema::hasTable('contact_inquiries'));
        $this->assertFalse(Schema::hasTable('services'));

        Artisan::call('migrate', ['--force' => true]);
        $this->assertTrue(Schema::hasTable('contact_inquiries'));
        $this->assertTrue(Schema::hasTable('audit_logs'));
    }
}
