<?php

namespace Tests\Feature\Admin;

use App\Enums\ContentStatus;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Industries\Pages\CreateIndustry;
use App\Filament\Resources\Portfolios\Pages\CreatePortfolio;
use App\Filament\Resources\Portfolios\Pages\EditPortfolio;
use App\Filament\Resources\Portfolios\Pages\ListPortfolios;
use App\Models\Client;
use App\Models\Industry;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_manager_can_create_and_edit_case_studies(): void
    {
        $user = User::factory()->withRole('Service Manager')->create();
        $industry = Industry::factory()->create();
        $existing = Portfolio::factory()->create();

        $this->actingAs($user);

        Livewire::test(ListPortfolios::class)->assertOk()->assertCanSeeTableRecords([$existing]);

        Livewire::test(CreatePortfolio::class)
            ->fillForm([
                'title' => 'Telehealth platform',
                'slug' => 'telehealth-platform',
                'summary' => 'A secure telehealth platform for clinics.',
                'client_label' => 'A regional clinic network',
                'industry_id' => $industry->id,
                'metrics' => [
                    ['value' => '30%', 'label' => 'Shorter waiting times', 'is_verified' => true],
                    ['value' => '2x', 'label' => 'Unverified claim', 'is_verified' => false],
                ],
                'status' => ContentStatus::Published->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $portfolio = Portfolio::query()->where('slug', 'telehealth-platform')->firstOrFail();
        $this->assertSame([['label' => 'Shorter waiting times', 'value' => '30%']], $portfolio->verifiedMetrics());

        Livewire::test(EditPortfolio::class, ['record' => $portfolio->getRouteKey()])
            ->assertOk()
            ->fillForm(['duration' => '5 months'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('5 months', $portfolio->refresh()->duration);
    }

    public function test_client_logo_approval_requires_a_reference(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        Livewire::test(CreateClient::class)
            ->fillForm(['name' => 'Acme Corp', 'logo_usage_approved' => true, 'logo_approved_at' => now()->toDateTimeString(), 'approval_reference' => null])
            ->call('create')
            ->assertHasFormErrors(['approval_reference' => 'required']);

        Livewire::test(CreateClient::class)
            ->fillForm(['name' => 'Acme Corp', 'logo_usage_approved' => true, 'logo_approved_at' => now()->toDateTimeString(), 'approval_reference' => 'Signed agreement 2026-03'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(Client::query()->where('name', 'Acme Corp')->value('logo_usage_approved'));
    }

    public function test_industry_manager_can_create_industries(): void
    {
        $this->actingAs(User::factory()->withRole('Service Manager')->create());

        Livewire::test(CreateIndustry::class)
            ->fillForm([
                'name' => 'Agritech',
                'slug' => 'agritech',
                'excerpt' => 'Digital tools for modern agriculture.',
                'challenges' => [['title' => 'Unpredictable yields', 'description' => null, 'icon' => 'chart-bar']],
                'status' => ContentStatus::Draft->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $industry = Industry::query()->where('slug', 'agritech')->firstOrFail();
        $this->assertSame('Unpredictable yields', $industry->challenges[0]['title']);
    }

    public function test_case_studies_require_permission(): void
    {
        $this->actingAs(User::factory()->withRole('Support Agent')->create())
            ->get('/admin/case-studies')
            ->assertForbidden();
    }
}
