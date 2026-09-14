<?php

namespace Tests\Feature;

use App\Enums\OpportunityStatus;
use App\Models\Category;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $otherUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['name' => 'Posting Owner']);
        $this->otherUser = User::factory()->create(['name' => 'Other User']);
        $this->category = Category::create([
            'name' => 'Technology & Software',
            'slug' => 'technology-software',
            'icon_svg' => '<svg></svg>',
        ]);
    }

    /** @test */
    public function unauthenticated_users_are_redirected_from_my_jobs()
    {
        $response = $this->get('/dashboard/my-jobs');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_view_their_job_postings()
    {
        $myJob = Opportunity::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Senior Full Stack Developer',
            'description' => 'Build modern Laravel web applications.',
            'location' => 'Lagos, Nigeria',
            'opportunity_type' => 'Full-time',
            'budget_min' => 500000,
            'budget_max' => 900000,
            'status' => OpportunityStatus::Open,
        ]);

        $otherJob = Opportunity::create([
            'user_id' => $this->otherUser->id,
            'category_id' => $this->category->id,
            'title' => 'UI/UX Designer',
            'description' => 'Design sleek dashboard interfaces.',
            'location' => 'Abuja, Nigeria',
            'opportunity_type' => 'Contract',
            'budget_min' => 300000,
            'budget_max' => 500000,
            'status' => OpportunityStatus::Open,
        ]);

        $response = $this->actingAs($this->owner)->get('/dashboard/my-jobs');

        $response->assertStatus(200);
        $response->assertSee('Senior Full Stack Developer');
        $response->assertDontSee('UI/UX Designer');
    }

    /** @test */
    public function owner_can_delete_their_own_opportunity()
    {
        $job = Opportunity::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Web Developer Job',
            'description' => 'To be deleted',
            'status' => OpportunityStatus::Open,
        ]);

        $response = $this->actingAs($this->owner)->delete("/opportunities/{$job->id}");

        $response->assertRedirect('/dashboard/my-jobs');
        $response->assertSessionHas('status');

        $this->assertSoftDeleted('opportunities', [
            'id' => $job->id,
        ]);
    }

    /** @test */
    public function non_owner_cannot_delete_another_users_opportunity()
    {
        $job = Opportunity::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'Owner Job Posting',
            'description' => 'Protected job posting',
            'status' => OpportunityStatus::Open,
        ]);

        $response = $this->actingAs($this->otherUser)->delete("/opportunities/{$job->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('opportunities', [
            'id' => $job->id,
            'deleted_at' => null,
        ]);
    }
}
