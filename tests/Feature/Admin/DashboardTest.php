<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\InquiriesChart;
use App\Filament\Widgets\LatestInquiries;
use App\Filament\Widgets\PendingTestimonials;
use App\Filament\Widgets\RecentActivity;
use App\Filament\Widgets\StatsOverview;
use App\Models\ContactInquiry;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_dashboard_renders_for_every_default_role(): void
    {
        foreach (array_keys(PermissionRegistry::roles()) as $role) {
            $this->actingAs(User::factory()->withRole($role)->create())
                ->get('/admin')
                ->assertOk();
        }
    }

    public function test_super_admin_can_open_every_platform_page(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $role = Role::findByName('Content Manager', 'web');
        $other = User::factory()->withRole('Blog Author')->create();

        $this->actingAs($superAdmin);

        foreach ([
            '/admin', '/admin/users', '/admin/users/create', '/admin/users/'.$other->id.'/edit',
            '/admin/roles', '/admin/roles/create', '/admin/roles/'.$role->id.'/edit',
            '/admin/audit-logs', '/admin/security-activity', '/admin/settings/company', '/admin/settings/forms-privacy',
            '/admin/settings/integrations', '/admin/settings/seo', '/admin/settings/animation', '/admin/theme-settings',
            '/admin/email-templates', '/admin/email-logs', '/admin/redirects', '/admin/redirects/create', '/admin/system-health',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_widgets_are_visible_only_with_relevant_permissions(): void
    {
        $this->actingAs(User::factory()->withRole('Blog Author')->create());
        $this->assertFalse(InquiriesChart::canView());
        $this->assertFalse(LatestInquiries::canView());
        $this->assertFalse(PendingTestimonials::canView());
        $this->assertFalse(RecentActivity::canView());

        $this->actingAs(User::factory()->withRole('Sales Manager')->create());
        $this->assertTrue(InquiriesChart::canView());
        $this->assertTrue(LatestInquiries::canView());
        $this->assertTrue(StatsOverview::canView());
        $this->assertFalse(RecentActivity::canView());
    }

    public function test_latest_inquiries_widget_respects_assignment_scope(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $mine = ContactInquiry::factory()->create(['assigned_to' => $agent->id]);
        $other = ContactInquiry::factory()->create();

        $this->actingAs($agent);

        Livewire::test(LatestInquiries::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_stats_and_pending_reviews_render_counts(): void
    {
        ContactInquiry::factory()->count(3)->create();
        Testimonial::factory()->create();

        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(StatsOverview::class)->assertOk()->assertSee('New inquiries (7 days)')->assertSee('Pending reviews');
        Livewire::test(PendingTestimonials::class)->assertOk()->assertCanSeeTableRecords(Testimonial::all());
        Livewire::test(InquiriesChart::class)->assertOk()->set('filter', '7')->assertOk();
    }
}
