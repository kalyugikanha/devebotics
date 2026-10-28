<?php

namespace Database\Seeders;

use App\Enums\InquiryTopic;
use App\Models\Team;
use Illuminate\Database\Seeder;

/**
 * Default query desk teams (no members — administrators add their own staff).
 * Idempotent: existing teams, including edits made in the admin, are kept.
 */
class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            'sales-new-projects' => [
                'name' => 'Sales & new projects',
                'description' => 'New project requests, quotes and partnership enquiries.',
                'color' => '#4f46e5',
                'topics' => [InquiryTopic::Sales->value, InquiryTopic::Partnership->value],
            ],
            'client-support' => [
                'name' => 'Client support',
                'description' => 'Questions and issues about existing projects and products.',
                'color' => '#0891b2',
                'topics' => [InquiryTopic::Support->value],
            ],
            'talent-hr' => [
                'name' => 'Talent & HR',
                'description' => 'Careers and hiring questions.',
                'color' => '#d97706',
                'topics' => [InquiryTopic::Careers->value],
            ],
        ];

        $order = 0;

        foreach ($teams as $slug => $attributes) {
            Team::query()->firstOrCreate(['slug' => $slug], $attributes + [
                'is_active' => true,
                'auto_assign' => false,
                'sort_order' => $order++,
            ]);
        }
    }
}
