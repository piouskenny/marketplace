<?php

namespace Tests\Feature;

use App\Enums\OpportunityStatus;
use App\Jobs\SendJobOpportunityAlertsJob;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Opportunity;
use App\Models\ProfessionalProfile;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\JobOpportunityAlertNotification;
use App\Services\JobMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class JobOpportunityAlertTest extends TestCase
{
    use RefreshDatabase;

    protected Category $educationCategory;
    protected Category $plumbingCategory;
    protected Subject $mathSubject;
    protected EducationLevel $secondaryLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->educationCategory = Category::create([
            'name' => 'Education & Tutoring',
            'slug' => 'education-tutoring',
        ]);

        $this->plumbingCategory = Category::create([
            'name' => 'Plumbing & Maintenance',
            'slug' => 'plumbing-maintenance',
        ]);

        $this->mathSubject = Subject::create([
            'name' => 'Mathematics',
            'slug' => 'mathematics',
        ]);

        $this->secondaryLevel = EducationLevel::create([
            'name' => 'Secondary School',
            'slug' => 'secondary-school',
        ]);
    }

    public function test_relevant_matching_professionals_receive_job_notifications()
    {
        Notification::fake();

        // 1. Create client user (job poster)
        $client = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // 2. Create matching Mathematics tutor
        $tutor = User::factory()->create([
            'name' => 'Math Expert',
            'location' => 'Lagos',
            'email_verified_at' => now(),
            'onboarding_completed' => true,
            'job_alerts_enabled' => true,
        ]);

        $tutorProfile = ProfessionalProfile::create([
            'user_id' => $tutor->id,
            'category_id' => $this->educationCategory->id,
            'display_name' => 'Senior Math Tutor',
            'location' => 'Lagos',
            'bio' => 'Specialized Mathematics tutor for secondary school students.',
        ]);

        $eduProfile = $tutorProfile->educationProfile()->create([
            'teaching_mode' => 'both',
        ]);
        $eduProfile->subjects()->attach($this->mathSubject->id);
        $eduProfile->educationLevels()->attach($this->secondaryLevel->id);

        // 3. Post a Mathematics job opportunity as Client
        $this->actingAs($client);
        $response = $this->post('/opportunities', [
            'title' => 'SS2 Mathematics Tutor Required',
            'category_id' => $this->educationCategory->id,
            'location' => 'Lagos',
            'opportunity_type' => 'physical',
            'description' => 'Looking for an experienced Mathematics tutor 3 times a week in Lagos.',
            'subject_id' => $this->mathSubject->id,
            'education_level_id' => $this->secondaryLevel->id,
            'teaching_mode' => 'physical',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('opportunities', [
            'title' => 'SS2 Mathematics Tutor Required',
            'user_id' => $client->id,
        ]);

        $opportunity = Opportunity::where('title', 'SS2 Mathematics Tutor Required')->first();

        // Run the matching job synchronously for testing
        (new SendJobOpportunityAlertsJob($opportunity))->handle(new JobMatchingService());

        // Assert tutor receives the notification
        Notification::assertSentTo(
            $tutor,
            JobOpportunityAlertNotification::class,
            function ($notification) use ($opportunity) {
                return $notification->opportunity->id === $opportunity->id;
            }
        );
    }

    public function test_unrelated_professionals_do_not_receive_job_notifications()
    {
        Notification::fake();

        $client = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // Create a Plumber user
        $plumber = User::factory()->create([
            'name' => 'Master Plumber',
            'location' => 'Abuja',
            'email_verified_at' => now(),
            'onboarding_completed' => true,
            'job_alerts_enabled' => true,
        ]);

        $plumberProfile = ProfessionalProfile::create([
            'user_id' => $plumber->id,
            'category_id' => $this->plumbingCategory->id,
            'display_name' => 'Certified Plumber',
            'location' => 'Abuja',
            'bio' => 'Expert pipe fitting and emergency leak repairs.',
        ]);

        // Post a Mathematics tutoring opportunity
        $opportunity = Opportunity::create([
            'user_id' => $client->id,
            'category_id' => $this->educationCategory->id,
            'title' => 'Calculus & Algebra Tutor Needed',
            'description' => 'Advanced mathematics tutoring needed for university prep.',
            'location' => 'Lagos',
            'opportunity_type' => 'physical',
            'status' => OpportunityStatus::Open,
        ]);

        (new SendJobOpportunityAlertsJob($opportunity))->handle(new JobMatchingService());

        // Assert plumber does NOT receive the notification
        Notification::assertNotSentTo($plumber, JobOpportunityAlertNotification::class);
    }

    public function test_job_owner_does_not_receive_their_own_job_alert()
    {
        Notification::fake();

        // User who is both a Math Tutor and posts a job
        $tutorClient = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
            'job_alerts_enabled' => true,
        ]);

        $profile = ProfessionalProfile::create([
            'user_id' => $tutorClient->id,
            'category_id' => $this->educationCategory->id,
            'display_name' => 'Math Teacher',
            'location' => 'Lagos',
        ]);
        $edu = $profile->educationProfile()->create(['teaching_mode' => 'both']);
        $edu->subjects()->attach($this->mathSubject->id);

        $opportunity = Opportunity::create([
            'user_id' => $tutorClient->id,
            'category_id' => $this->educationCategory->id,
            'title' => 'Math Tutor Needed for My Son',
            'description' => 'Private math tutoring in Lagos.',
            'location' => 'Lagos',
            'opportunity_type' => 'physical',
            'status' => OpportunityStatus::Open,
        ]);

        (new SendJobOpportunityAlertsJob($opportunity))->handle(new JobMatchingService());

        // Assert job poster does NOT receive notification for their own job
        Notification::assertNotSentTo($tutorClient, JobOpportunityAlertNotification::class);
    }

    public function test_opted_out_users_and_unverified_users_do_not_receive_notifications()
    {
        Notification::fake();

        $client = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // Opted out user
        $optedOutTutor = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
            'job_alerts_enabled' => false,
        ]);
        $p1 = ProfessionalProfile::create([
            'user_id' => $optedOutTutor->id,
            'category_id' => $this->educationCategory->id,
            'display_name' => 'Opted Out Tutor',
            'location' => 'Lagos',
        ]);
        $p1->educationProfile()->create()->subjects()->attach($this->mathSubject->id);

        // Unverified user
        $unverifiedTutor = User::factory()->create([
            'email_verified_at' => null,
            'onboarding_completed' => true,
            'job_alerts_enabled' => true,
        ]);
        $p2 = ProfessionalProfile::create([
            'user_id' => $unverifiedTutor->id,
            'category_id' => $this->educationCategory->id,
            'display_name' => 'Unverified Tutor',
            'location' => 'Lagos',
        ]);
        $p2->educationProfile()->create()->subjects()->attach($this->mathSubject->id);

        $opportunity = Opportunity::create([
            'user_id' => $client->id,
            'category_id' => $this->educationCategory->id,
            'title' => 'High School Math Teacher Needed',
            'description' => 'Mathematics teacher for secondary school.',
            'location' => 'Lagos',
            'opportunity_type' => 'physical',
            'status' => OpportunityStatus::Open,
        ]);

        (new SendJobOpportunityAlertsJob($opportunity))->handle(new JobMatchingService());

        Notification::assertNotSentTo($optedOutTutor, JobOpportunityAlertNotification::class);
        Notification::assertNotSentTo($unverifiedTutor, JobOpportunityAlertNotification::class);
    }

    public function test_duplicate_events_do_not_send_duplicate_emails()
    {
        Notification::fake();

        $client = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $tutor = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
            'job_alerts_enabled' => true,
        ]);
        $p = ProfessionalProfile::create([
            'user_id' => $tutor->id,
            'category_id' => $this->educationCategory->id,
            'display_name' => 'Math Teacher',
            'location' => 'Lagos',
        ]);
        $p->educationProfile()->create()->subjects()->attach($this->mathSubject->id);

        $opportunity = Opportunity::create([
            'user_id' => $client->id,
            'category_id' => $this->educationCategory->id,
            'title' => 'Math Tutor Needed Urgently',
            'description' => 'Private math tutor needed in Lagos.',
            'location' => 'Lagos',
            'opportunity_type' => 'physical',
            'status' => OpportunityStatus::Open,
        ]);

        $job = new SendJobOpportunityAlertsJob($opportunity);
        $service = new JobMatchingService();

        // First run
        $job->handle($service);
        Notification::assertSentToTimes($tutor, JobOpportunityAlertNotification::class, 1);

        // Second run (duplicate trigger)
        $job->handle($service);
        // Times sent should still be 1 due to deduplication cache check
        Notification::assertSentToTimes($tutor, JobOpportunityAlertNotification::class, 1);
    }

    public function test_user_can_unsubscribe_via_signed_url()
    {
        $user = User::factory()->create([
            'job_alerts_enabled' => true,
        ]);

        $unsubscribeUrl = URL::signedRoute('job-alerts.unsubscribe', ['user' => $user->id]);

        $response = $this->get($unsubscribeUrl);
        $response->assertOk();
        $response->assertSee('Successfully Unsubscribed');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'job_alerts_enabled' => false,
        ]);
    }
}
