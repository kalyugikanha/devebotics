<?php

namespace Tests\Feature\Admin;

use App\Enums\InquiryActivityType;
use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Enums\TeamRole;
use App\Filament\Resources\ContactInquiries\Pages\ListContactInquiries;
use App\Filament\Resources\ContactInquiries\Pages\ViewContactInquiry;
use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Filament\Widgets\QueryDeskOverview;
use App\Mail\TemplatedMail;
use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use App\Services\Crm\InquiryManager;
use App\Services\Crm\InquiryRouter;
use Database\Seeders\CoreContentSeeder;
use Database\Seeders\TeamSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use RuntimeException;
use Tests\Concerns\SubmitsPublicForms;
use Tests\TestCase;

class QueryDeskTest extends TestCase
{
    use RefreshDatabase, SubmitsPublicForms;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function submit(array $overrides = []): ContactInquiry
    {
        $this->post('/contact', $this->withSpamProtection(array_merge([
            'type' => 'quick',
            'topic' => InquiryTopic::Support->value,
            'name' => 'Ada Lovelace',
            'email' => 'ada'.uniqid().'@example.test',
            'message' => 'Our portal shows an error when exporting monthly reports.',
            'privacy_consent' => '1',
        ], $overrides)))->assertSessionHasNoErrors();

        return ContactInquiry::query()->latest('id')->firstOrFail();
    }

    public function test_inquiry_is_routed_by_topic_and_the_team_queue_is_notified(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $outsider = User::factory()->withRole('Support Agent')->create();
        Team::factory()->handling(InquiryTopic::Sales)->create();
        $support = Team::factory()->handling(InquiryTopic::Support)->withMembers([$agent])->create(['email' => 'support@example.test']);

        $inquiry = $this->submit();

        $this->assertSame($support->id, $inquiry->team_id);
        $this->assertNull($inquiry->assigned_to);
        $this->assertDatabaseHas('inquiry_activities', ['contact_inquiry_id' => $inquiry->id, 'type' => InquiryActivityType::Routed->value]);
        $this->assertSame(1, $agent->notifications()->count());
        $this->assertSame(0, $outsider->notifications()->count());
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'inquiry_notification' && $mail->hasTo('support@example.test'));
    }

    public function test_service_routing_and_topic_plus_service_preference(): void
    {
        $this->seed(CoreContentSeeder::class);
        $service = Service::query()->firstOrFail();

        $byService = Team::factory()->create(['sort_order' => 0]);
        $byService->services()->attach($service);
        $generalSupport = Team::factory()->handling(InquiryTopic::Support)->create(['sort_order' => 1]);
        $specialist = Team::factory()->handling(InquiryTopic::Support)->create(['sort_order' => 2]);
        $specialist->services()->attach($service);

        $this->assertSame($byService->id, $this->submit(['topic' => InquiryTopic::Other->value, 'service_id' => $service->id])->team_id);
        $this->assertSame($specialist->id, $this->submit(['service_id' => $service->id])->team_id);
        $this->assertSame($generalSupport->id, $this->submit()->team_id);
        $this->assertNull($this->submit(['topic' => InquiryTopic::Careers->value])->team_id, 'No team handles careers: stays unrouted.');
    }

    public function test_inactive_teams_receive_nothing(): void
    {
        Team::factory()->handling(InquiryTopic::Support)->create(['is_active' => false]);

        $this->assertNull($this->submit()->team_id);
    }

    public function test_round_robin_picks_the_member_with_fewest_open_inquiries_then_least_recently_assigned(): void
    {
        [$busy, $free, $inactive] = User::factory()->withRole('Support Agent')->count(3)->create();
        $inactive->forceFill(['is_active' => false])->save();
        Team::factory()->handling(InquiryTopic::Support)->autoAssign()->withMembers([$busy, $free, $inactive])->create();
        ContactInquiry::factory()->create(['assigned_to' => $busy->id]);

        $first = $this->submit();
        $this->assertSame($free->id, $first->assigned_to);
        $this->assertSame(1, $free->notifications()->count());
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'inquiry_assigned' && $mail->hasTo($free->email));

        // Both now have one open inquiry; the one assigned least recently is next.
        $this->assertSame($busy->id, $this->submit()->assigned_to);
        $this->assertNotSame($inactive->id, $this->submit()->assigned_to);
    }

    public function test_routing_failures_never_break_the_public_form(): void
    {
        $this->mock(InquiryRouter::class)->shouldReceive('route')->andThrow(new RuntimeException('Routing exploded'));

        $inquiry = $this->submit();

        $this->assertNull($inquiry->team_id);
        $this->assertSame(InquiryStatus::New, $inquiry->status);
    }

    public function test_team_members_see_their_team_queue_but_not_other_teams(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $team = Team::factory()->withMembers([$agent])->create();
        $otherTeam = Team::factory()->create();

        $queued = ContactInquiry::factory()->create(['team_id' => $team->id]);
        $foreign = ContactInquiry::factory()->create(['team_id' => $otherTeam->id]);
        $unrouted = ContactInquiry::factory()->create();

        $this->actingAs($agent);

        Livewire::test(ListContactInquiries::class)
            ->set('activeTab', 'team')
            ->assertCanSeeTableRecords([$queued])
            ->assertCanNotSeeTableRecords([$foreign, $unrouted])
            ->set('activeTab', 'all')
            ->assertCanNotSeeTableRecords([$foreign, $unrouted]);

        $this->get(route('filament.admin.resources.contact-inquiries.view', $queued))->assertOk();
        $this->get(route('filament.admin.resources.contact-inquiries.view', $foreign))->assertNotFound();
        $this->get(route('files.inquiry-attachment', $foreign))->assertForbidden();
        $this->assertFalse($agent->can('view', $foreign));
        $this->assertTrue($agent->can('view', $queued));
    }

    public function test_members_can_pick_up_unassigned_team_inquiries_once(): void
    {
        [$first, $second] = User::factory()->withRole('Support Agent')->count(2)->create();
        $team = Team::factory()->withMembers([$first, $second])->create();
        $inquiry = ContactInquiry::factory()->create(['team_id' => $team->id]);

        $this->actingAs($first);
        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction('claim')
            ->assertHasNoActionErrors();

        $this->assertSame($first->id, $inquiry->refresh()->assigned_to);
        $this->assertNotNull($team->memberships()->where('user_id', $first->id)->value('last_assigned_at'));

        $this->actingAs($second);
        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])->assertActionHidden('claim');

        $this->expectException(AuthorizationException::class);
        app(InquiryManager::class)->claim($second, $inquiry);
    }

    public function test_users_without_team_access_cannot_pick_up(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $agent->syncPermissions([]);
        $agent->syncRoles([]);
        $agent->givePermissionTo(['dashboard.view', 'inquiries.view', 'inquiries.update']);
        $team = Team::factory()->withMembers([$agent])->create();
        $inquiry = ContactInquiry::factory()->create(['team_id' => $team->id]);

        $this->assertFalse($agent->can('claim', $inquiry));
        $this->assertFalse($agent->can('view', $inquiry));
    }

    public function test_team_leads_assign_only_within_their_team(): void
    {
        $lead = User::factory()->withRole('Team Lead')->create();
        $member = User::factory()->withRole('Support Agent')->create();
        $outsider = User::factory()->withRole('Support Agent')->create();
        $team = Team::factory()->withMembers([$member])->withMembers([$lead], TeamRole::Lead)->create();
        Team::factory()->withMembers([$outsider])->create();
        $inquiry = ContactInquiry::factory()->create(['team_id' => $team->id]);

        $this->actingAs($lead);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->assertActionHidden('moveToTeam')
            ->callAction('assign', data: ['assigned_to' => $outsider->id])
            ->assertHasActionErrors(['assigned_to']);

        $this->assertNull($inquiry->refresh()->assigned_to);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction('assign', data: ['assigned_to' => $member->id])
            ->assertHasNoActionErrors();

        $this->assertSame($member->id, $inquiry->refresh()->assigned_to);
        $this->assertSame(1, $member->notifications()->count());
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'inquiry_assigned' && $mail->hasTo($member->email));

        // The service layer enforces the same rule, whatever the UI sends.
        try {
            app(InquiryManager::class)->assign($lead, $inquiry, $outsider);
            $this->fail('Assigning outside the team must be rejected.');
        } catch (ValidationException) {
            $this->assertSame($member->id, $inquiry->refresh()->assigned_to);
        }
    }

    public function test_managers_move_inquiries_between_teams(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        $agent = User::factory()->withRole('Support Agent')->create();
        $from = Team::factory()->withMembers([$agent])->create();
        $to = Team::factory()->create();
        $inquiry = ContactInquiry::factory()->create(['team_id' => $from->id, 'assigned_to' => $agent->id]);

        $this->actingAs($manager);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction('moveToTeam', data: ['team_id' => $to->id])
            ->assertHasNoActionErrors();

        $inquiry->refresh();
        $this->assertSame($to->id, $inquiry->team_id);
        $this->assertNull($inquiry->assigned_to, 'The assignee is not in the new team, so the assignment is cleared.');
        $this->assertFalse($agent->can('view', $inquiry));
    }

    public function test_closing_statuses_set_closed_at_and_the_first_move_records_the_first_response(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        $inquiry = ContactInquiry::factory()->create();
        $manager2 = app(InquiryManager::class);

        $manager2->changeStatus($manager, $inquiry, InquiryStatus::InProgress);
        $inquiry->refresh();
        $this->assertNotNull($inquiry->first_response_at);
        $this->assertNull($inquiry->closed_at);

        foreach ([InquiryStatus::Resolved, InquiryStatus::Lost, InquiryStatus::Converted, InquiryStatus::Closed] as $status) {
            $manager2->changeStatus($manager, $inquiry, InquiryStatus::OnHold);
            $manager2->changeStatus($manager, $inquiry, $status);
            $this->assertNotNull($inquiry->refresh()->closed_at, $status->value);
        }

        $manager2->changeStatus($manager, $inquiry, InquiryStatus::InProgress);
        $this->assertNull($inquiry->refresh()->closed_at);
    }

    public function test_overdue_inquiries_follow_the_response_target(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        $late = ContactInquiry::factory()->create(['created_at' => now()->subHours(30)]);
        $answered = ContactInquiry::factory()->create(['created_at' => now()->subHours(30), 'first_response_at' => now()->subHours(29)]);
        $fresh = ContactInquiry::factory()->create();

        $this->actingAs($manager);

        Livewire::test(ListContactInquiries::class)
            ->set('activeTab', 'overdue')
            ->assertCanSeeTableRecords([$late])
            ->assertCanNotSeeTableRecords([$answered, $fresh]);

        $this->assertTrue($late->isOverdue());

        settings()->setMany('forms', ['response_target_hours' => 48]);

        $this->assertFalse($late->fresh()->isOverdue());
        $this->assertSame(0, ContactInquiry::query()->overdue()->count());
    }

    public function test_team_resource_is_limited_to_team_managers_and_validates_members(): void
    {
        $this->actingAs(User::factory()->withRole('Support Agent')->create())->get('/admin/teams')->assertForbidden();

        $manager = User::factory()->withRole('Sales Manager')->create();
        $agent = User::factory()->withRole('Support Agent')->create();
        $deactivated = User::factory()->withRole('Support Agent')->create(['is_active' => false]);
        $author = User::factory()->withRole('Blog Author')->create();

        $this->actingAs($manager)->get('/admin/teams')->assertOk();

        $undo = Repeater::fake();

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Escalations',
                'topics' => [InquiryTopic::Support->value],
                'memberships' => [
                    ['user_id' => $deactivated->id, 'role' => TeamRole::Member->value],
                    ['user_id' => $author->id, 'role' => TeamRole::Member->value],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['memberships.0.user_id', 'memberships.1.user_id']);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Escalations',
                'topics' => [InquiryTopic::Support->value],
                'auto_assign' => true,
                'memberships' => [['user_id' => $agent->id, 'role' => TeamRole::Lead->value]],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undo();

        $team = Team::query()->where('slug', 'escalations')->sole();
        $this->assertTrue($team->handlesTopic(InquiryTopic::Support));
        $this->assertTrue($team->isLead($agent));
        $this->assertTrue($team->auto_assign);
    }

    public function test_query_desk_pages_render_for_a_super_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $team = Team::factory()->handling(InquiryTopic::Sales)->withMembers([$admin], TeamRole::Lead)->create();
        $inquiry = ContactInquiry::factory()->create(['team_id' => $team->id, 'created_at' => now()->subDays(3)]);

        $this->actingAs($admin);

        foreach (['' => true, '?tab=team' => true, '?tab=unassigned' => true, '?tab=overdue' => true, '?tab=mine' => false, '?tab=closed' => false] as $query => $listed) {
            $response = $this->get('/admin/contact-inquiries'.$query)->assertOk();
            $listed ? $response->assertSee($inquiry->reference) : $response->assertDontSee($inquiry->reference);
        }

        $this->get(route('filament.admin.resources.contact-inquiries.view', $inquiry))->assertOk()->assertSee($team->name);
        $this->get('/admin/teams')->assertOk()->assertSee($team->name);
        $this->get('/admin/teams/create')->assertOk();
        $this->get('/admin/teams/'.$team->id.'/edit')->assertOk();
        $this->get('/admin')->assertOk();

        Livewire::test(QueryDeskOverview::class)
            ->assertSee('My query desk')
            ->assertSee('Waiting in my teams')
            ->assertSee('Overdue first responses');
    }

    public function test_dashboard_widget_and_default_teams(): void
    {
        $this->actingAs(User::factory()->withRole('Support Agent')->create());
        $this->assertTrue(QueryDeskOverview::canView());

        $this->actingAs(User::factory()->withRole('Blog Author')->create());
        $this->assertFalse(QueryDeskOverview::canView());

        $this->seed(TeamSeeder::class);
        $this->seed(TeamSeeder::class);

        $this->assertSame(3, Team::query()->count());
        $this->assertSame(0, Team::query()->has('members')->count(), 'No fake people are seeded.');
        $this->assertTrue(Team::query()->where('slug', 'client-support')->sole()->handlesTopic(InquiryTopic::Support));
    }
}
