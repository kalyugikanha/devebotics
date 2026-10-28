<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\ContentStatus;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use App\Filament\Resources\JobOpenings\Pages\CreateJobOpening;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CareersAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_manager_creates_a_job_opening(): void
    {
        $hr = User::factory()->withRole('HR Manager')->create();
        $this->actingAs($hr);

        Livewire::test(CreateJobOpening::class)
            ->fillForm([
                'title' => 'Senior DevOps Engineer',
                'slug' => 'senior-devops-engineer',
                'summary' => 'Own our CI/CD pipelines and cloud infrastructure.',
                'employment_type' => 'full_time',
                'workplace_type' => 'remote',
                'status' => ContentStatus::Published->value,
                'show_salary' => true,
                'salary_min' => 90000,
                'salary_max' => 120000,
                'salary_currency' => 'usd',
                'valid_through' => now()->addMonth()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $job = JobOpening::query()->where('slug', 'senior-devops-engineer')->firstOrFail();
        $this->assertSame('USD', $job->salary_currency);
        $this->assertTrue($job->isOpen());
    }

    public function test_hr_manager_reviews_an_application(): void
    {
        $hr = User::factory()->withRole('HR Manager')->create();
        $application = JobApplication::factory()->create();

        $this->actingAs($hr);

        Livewire::test(ListJobApplications::class)->assertCanSeeTableRecords([$application]);

        Livewire::test(ViewJobApplication::class, ['record' => $application->getRouteKey()])
            ->callAction('changeStatus', data: ['status' => ApplicationStatus::Shortlisted->value])
            ->callAction('rate', data: ['rating' => 4])
            ->callAction('notes', data: ['internal_notes' => 'Strong Kubernetes background.']);

        $application->refresh();
        $this->assertSame(ApplicationStatus::Shortlisted, $application->status);
        $this->assertSame(4, $application->rating);
        $this->assertSame('Strong Kubernetes background.', $application->internal_notes);
        $this->assertSame($hr->id, $application->reviewed_by);
    }

    public function test_users_without_application_access_are_forbidden(): void
    {
        $editor = User::factory()->withRole('Blog Editor')->create();
        $application = JobApplication::factory()->create();

        $this->actingAs($editor);

        $this->get(route('filament.admin.resources.job-applications.index'))->assertForbidden();
        $this->get(route('filament.admin.resources.job-applications.view', $application))->assertForbidden();
        $this->get(route('files.resume', $application))->assertForbidden();
    }
}
