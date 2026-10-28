<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\Statistics\Pages\ManageStatistics;
use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Models\Achievement;
use App\Models\Client;
use App\Models\Industry;
use App\Models\Media;
use App\Models\NavigationMenu;
use App\Models\Partner;
use App\Models\Statistic;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CompanyResourcesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function adminPages(): array
    {
        return collect([
            '/admin/service-categories', '/admin/technologies', '/admin/products', '/admin/products/create',
            '/admin/product-categories', '/admin/industries', '/admin/industries/create', '/admin/case-studies',
            '/admin/case-studies/create', '/admin/portfolio-categories', '/admin/clients', '/admin/clients/create',
            '/admin/partners', '/admin/partners/create', '/admin/team-members', '/admin/team-members/create',
            '/admin/milestones', '/admin/achievements', '/admin/achievements/create', '/admin/statistics',
            '/admin/faqs', '/admin/faq-categories', '/admin/pages', '/admin/pages/create', '/admin/navigation-menus',
            '/admin/navigation-menus/create', '/admin/announcements', '/admin/media', '/admin/media/create',
        ])->mapWithKeys(fn (string $url) => [$url => [$url]])->all();
    }

    #[DataProvider('adminPages')]
    public function test_super_admin_can_open_every_content_admin_page(string $url): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())->get($url)->assertOk();
    }

    public function test_super_admin_can_open_edit_pages(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        $menu = NavigationMenu::query()->create(['name' => 'Footer', 'location' => 'footer_1', 'is_active' => true]);
        $menu->allItems()->create(['label' => 'About', 'link_type' => 'route', 'route_name' => 'about', 'sort_order' => 0]);
        $achievement = Achievement::query()->create(['title' => 'Award', 'type' => 'award', 'is_verified' => true, 'is_active' => true]);

        $urls = [
            '/admin/industries/'.Industry::factory()->create()->id.'/edit',
            '/admin/clients/'.Client::factory()->create()->id.'/edit',
            '/admin/partners/'.Partner::factory()->create()->id.'/edit',
            '/admin/team-members/'.TeamMember::factory()->create()->id.'/edit',
            '/admin/achievements/'.$achievement->id.'/edit',
            '/admin/navigation-menus/'.$menu->id.'/edit',
            '/admin/media/'.Media::factory()->create()->id.'/edit',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_hr_manager_can_view_but_not_manage_company_content(): void
    {
        $user = User::factory()->withRole('HR Manager')->create();

        $this->actingAs($user)->get('/admin/team-members')->assertOk();
        $this->actingAs($user)->get('/admin/team-members/create')->assertForbidden();
        $this->actingAs($user)->get('/admin/clients')->assertOk();
        $this->actingAs($user)->get('/admin/clients/create')->assertForbidden();
    }

    public function test_content_manager_can_add_team_members_and_achievements(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        Livewire::test(CreateTeamMember::class)
            ->fillForm(['name' => 'Ada Lovelace', 'job_title' => 'Principal Engineer', 'is_leadership' => true, 'linkedin_url' => 'https://www.linkedin.com/in/example'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(TeamMember::query()->where('name', 'Ada Lovelace')->value('is_leadership'));

        Livewire::test(CreateAchievement::class)
            ->fillForm(['title' => 'ISO 27001', 'type' => 'certification', 'issuer' => 'Example Registrar', 'is_verified' => false])
            ->call('create')
            ->assertHasNoFormErrors();

        $achievement = Achievement::query()->where('title', 'ISO 27001')->firstOrFail();
        $this->assertFalse(Achievement::query()->publishable()->whereKey($achievement->id)->exists(), 'Unverified achievements must stay hidden.');
    }

    public function test_statistics_are_hidden_until_verified(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        Livewire::test(ManageStatistics::class)
            ->callAction(CreateAction::class, ['label' => 'Projects delivered', 'value' => 50, 'decimals' => 0, 'suffix' => '+', 'is_verified' => false, 'is_active' => true])
            ->assertHasNoActionErrors();

        $statistic = Statistic::query()->where('label', 'Projects delivered')->firstOrFail();
        $this->assertSame('50+', $statistic->formattedValue());
        $this->assertFalse(Statistic::query()->publishable()->exists());
    }
}
