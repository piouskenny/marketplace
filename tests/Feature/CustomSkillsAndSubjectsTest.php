<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Opportunity;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\TalentType;
use App\Models\User;
use Database\Seeders\TalentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomSkillsAndSubjectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TalentTypeSeeder::class);
    }

    public function test_custom_skills_are_auto_created_and_linked_during_professional_onboarding()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'IT & Software', 'slug' => 'it-software']);
        $existingSkill = Skill::create(['name' => 'PHP', 'slug' => 'php']);

        $response = $this->actingAs($user)->post(url('/onboarding/professional'), [
            'category_id' => $category->id,
            'display_name' => 'Jane Developer',
            'bio' => 'Senior Laravel Developer offering remote software services.',
            'years_of_experience' => 5,
            'location' => 'Lagos, Nigeria',
            'phone' => '+2348012345678',
            'skills' => [$existingSkill->id],
            'custom_skills' => 'Flutter Mobile Dev, AI Prompt Engineering',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skills', ['name' => 'Flutter Mobile Dev', 'slug' => 'flutter-mobile-dev']);
        $this->assertDatabaseHas('skills', ['name' => 'AI Prompt Engineering', 'slug' => 'ai-prompt-engineering']);

        $profile = $user->fresh()->professionalProfile;
        $this->assertCount(3, $profile->skills);
    }

    public function test_custom_subjects_are_auto_created_and_linked_during_tutor_onboarding()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::create(['name' => 'Academic Tutoring', 'slug' => 'academic-tutoring']);
        $level = EducationLevel::create(['name' => 'Senior Secondary (SS1-SS3)', 'slug' => 'senior-secondary']);

        $teacherType = TalentType::where('slug', 'teacher')->first();
        $user->talentTypes()->attach($teacherType->id, ['completed_at' => null]);

        // Set up base professional profile with valid category_id
        $user->professionalProfile()->create([
            'category_id' => $category->id,
            'display_name' => 'Tutor Jane',
            'bio' => 'Experienced academic tutor in Ikeja.',
            'location' => 'Lagos, Nigeria',
        ]);

        $response = $this->actingAs($user)->post(url('/onboarding/tutor'), [
            'teaching_mode' => 'both',
            'qualifications' => 'B.Sc. Mathematics',
            'level_ids' => [$level->id],
            'custom_subjects' => 'Further Mathematics, SAT Prep',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subjects', ['name' => 'Further Mathematics', 'slug' => 'further-mathematics']);
        $this->assertDatabaseHas('subjects', ['name' => 'SAT Prep', 'slug' => 'sat-prep']);

        $eduProfile = $user->fresh()->professionalProfile->educationProfile;
        $this->assertCount(2, $eduProfile->subjects);
    }

    public function test_custom_skills_and_subjects_can_be_added_via_profile_edit()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::create(['name' => 'General Service', 'slug' => 'general-service']);
        $level = EducationLevel::create(['name' => 'Primary', 'slug' => 'primary']);

        $profProfile = $user->professionalProfile()->create([
            'category_id' => $category->id,
            'display_name' => 'Test User',
            'bio' => 'Base bio for profile edit test.',
            'location' => 'Lagos, Nigeria',
        ]);
        $profProfile->educationProfile()->create([
            'teaching_mode' => 'both',
        ]);

        $response = $this->actingAs($user)->put(url('/profile/edit'), [
            'name' => 'Updated User Name',
            'phone' => '+2348099998888',
            'location' => 'Abuja, Nigeria',
            'category_id' => $category->id,
            'bio' => 'Updated biography for testing profile edit.',
            'custom_skills' => 'Solar Inverter Setup, Smart Home Cabling',
            'custom_subjects' => 'Phonics & Diction, Igbo Language',
            'level_ids' => [$level->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skills', ['name' => 'Solar Inverter Setup']);
        $this->assertDatabaseHas('skills', ['name' => 'Smart Home Cabling']);
        $this->assertDatabaseHas('subjects', ['name' => 'Phonics & Diction']);
        $this->assertDatabaseHas('subjects', ['name' => 'Igbo Language']);
    }

    public function test_opportunity_creation_with_custom_subject()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::create(['name' => 'Academic Tutoring', 'slug' => 'academic-tutoring']);
        $level = EducationLevel::create(['name' => 'Senior Secondary', 'slug' => 'senior-secondary-opp']);

        $response = $this->actingAs($user)->post(url('/opportunities'), [
            'title' => 'WAEC Igbo Language Home Tutor Needed',
            'category_id' => $category->id,
            'opportunity_type' => 'Physical In-Person',
            'location' => 'Enugu, Nigeria',
            'budget' => '₦20,000',
            'description' => 'Looking for an experienced Igbo language tutor for SS3 WAEC candidate.',
            'education_level_id' => $level->id,
            'custom_subject' => 'Igbo Native Language',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subjects', ['name' => 'Igbo Native Language']);

        $opportunity = Opportunity::latest()->first();
        $this->assertEquals('Igbo Native Language', $opportunity->educationDetails->subject->name);
    }
}

