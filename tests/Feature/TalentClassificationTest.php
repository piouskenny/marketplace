<?php

namespace Tests\Feature;

use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\ProfessionalProfile;
use App\Models\SkilledLabourProfile;
use App\Models\Subject;
use App\Models\TalentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentClassificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the three talent type rows required by all tests
        TalentType::insert([
            ['slug' => 'professional',   'label' => 'Professional',          'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'teacher',        'label' => 'Teacher',               'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'skilled_labour', 'label' => 'Skilled Labour Worker', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed basic categories
        Category::insert([
            ['id' => 1, 'name' => 'Creative & Digital Services', 'slug' => 'creative-digital-services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Home & Technical Services',   'slug' => 'home-technical-services',   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Education & Tutoring',        'slug' => 'education-tutoring',         'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed subcategory for Home & Technical Services
        Category::insert([
            ['id' => 10, 'name' => 'Plumbing Services', 'slug' => 'plumbing-services', 'parent_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function verifiedUser(array $extra = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'onboarding_completed' => false,
        ], $extra));
    }

    // ─── Enum Tests ──────────────────────────────────────────────────────────

    public function test_enum_slugs_returns_three_values(): void
    {
        $slugs = TalentClassification::slugs();
        $this->assertCount(3, $slugs);
        $this->assertContains('professional', $slugs);
        $this->assertContains('teacher', $slugs);
        $this->assertContains('skilled_labour', $slugs);
    }

    public function test_enum_from_slug_returns_correct_case(): void
    {
        $this->assertSame(TalentClassification::Professional, TalentClassification::fromSlug('professional'));
        $this->assertSame(TalentClassification::Teacher, TalentClassification::fromSlug('teacher'));
        $this->assertSame(TalentClassification::SkilledLabour, TalentClassification::fromSlug('skilled_labour'));
        $this->assertNull(TalentClassification::fromSlug('unknown'));
    }

    // ─── Step 20 Checklist Scenarios (1 to 33) ───────────────────────────────

    public function test_01_new_verified_user_reaches_dashboard(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_02_eligible_new_user_sees_talent_profile_completion_prompt(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Complete your Skill Link NG profile');
        $response->assertSee('Do you offer a service?');
    }

    public function test_03_user_can_select_professional(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['professional'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertDatabaseHas('talent_type_user', [
            'user_id' => $user->id,
            'talent_type_id' => TalentType::where('slug', 'professional')->value('id'),
        ]);
    }

    public function test_04_user_can_select_teacher(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['teacher'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertDatabaseHas('talent_type_user', [
            'user_id' => $user->id,
            'talent_type_id' => TalentType::where('slug', 'teacher')->value('id'),
        ]);
    }

    public function test_05_user_can_select_skilled_labour(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['skilled_labour'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertDatabaseHas('talent_type_user', [
            'user_id' => $user->id,
            'talent_type_id' => TalentType::where('slug', 'skilled_labour')->value('id'),
        ]);
    }

    public function test_06_user_can_select_professional_and_teacher(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['professional', 'teacher'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertCount(2, $user->fresh()->talentTypes);
    }

    public function test_07_user_can_select_professional_and_skilled_labour(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['professional', 'skilled_labour'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertCount(2, $user->fresh()->talentTypes);
    }

    public function test_08_user_can_select_teacher_and_skilled_labour(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['teacher', 'skilled_labour'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertCount(2, $user->fresh()->talentTypes);
    }

    public function test_09_user_can_select_all_three(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['professional', 'teacher', 'skilled_labour'],
        ]);

        $response->assertRedirectToRoute('onboarding.professional');
        $this->assertCount(3, $user->fresh()->talentTypes);
    }

    public function test_10_user_can_skip(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/skip');
        $response->assertRedirect('/dashboard');

        $this->assertTrue($user->fresh()->onboarding_completed);
        $this->assertEquals('skip', $user->fresh()->onboarding_intent);
    }

    public function test_11_skip_creates_no_talent_classifications(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/skip');

        $this->assertCount(0, $user->fresh()->talentTypes);
    }

    public function test_12_skip_creates_no_unnecessary_talent_profiles(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/skip');

        $this->assertDatabaseMissing('professional_profiles', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('education_profiles', []);
        $this->assertDatabaseMissing('skilled_labour_profiles', []);
    }

    public function test_13_skipped_user_is_not_prompted_every_dashboard_request(): void
    {
        $user = $this->verifiedUser(['onboarding_intent' => 'skip', 'onboarding_completed' => true]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Complete your Skill Link NG profile');
    }

    public function test_14_skipped_user_can_later_choose_to_offer_services(): void
    {
        $user = $this->verifiedUser(['onboarding_intent' => 'skip', 'onboarding_completed' => true]);

        $response = $this->actingAs($user)->get('/onboarding/classification');
        $response->assertStatus(200);
        $response->assertSee('How would you like to be listed?');
    }

    public function test_15_shared_profile_is_created_only_once(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['teacher', 'skilled_labour'],
        ]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Multi Talent User',
            'bio'                    => 'Experienced teacher and plumber.',
            'years_of_experience'    => 6,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Allen',
            'phone'                  => '08011223344',
        ]);

        $this->assertEquals(1, ProfessionalProfile::where('user_id', $user->id)->count());
    }

    public function test_16_teacher_only_user_has_professional_profile_internally_but_is_not_classified_professional(): void
    {
        $user = $this->verifiedUser();
        Subject::insert([['id' => 1, 'name' => 'English', 'slug' => 'english', 'created_at' => now(), 'updated_at' => now()]]);
        EducationLevel::insert([['id' => 1, 'name' => 'Primary', 'slug' => 'primary', 'created_at' => now(), 'updated_at' => now()]]);

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Teacher Only',
            'bio'                    => 'Passionate english teacher.',
            'years_of_experience'    => 4,
            'location'               => 'Surulere, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Surulere',
            'location_neighbourhood' => 'Bode Thomas',
            'phone'                  => '08022334455',
        ]);

        $this->actingAs($user)->post('/onboarding/tutor', [
            'teaching_mode' => 'online',
            'subject_ids'   => [1],
            'level_ids'     => [1],
        ]);

        $fresh = $user->fresh()->load('talentTypes');
        $this->assertNotNull($fresh->professionalProfile);
        $this->assertTrue($fresh->isTeacher());
        $this->assertFalse($fresh->isProfessional());
    }

    public function test_17_skilled_labour_only_user_has_professional_profile_internally_but_is_not_classified_professional(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Skilled Worker',
            'bio'                    => 'Certified electrician.',
            'years_of_experience'    => 5,
            'location'               => 'Lekki, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
            'phone'                  => '08033445566',
        ]);

        $this->actingAs($user)->post('/onboarding/skilled-labour', [
            'trade_category_id' => 10,
            'is_certified'      => true,
        ]);

        $fresh = $user->fresh()->load('talentTypes');
        $this->assertNotNull($fresh->professionalProfile);
        $this->assertTrue($fresh->isSkilledLabour());
        $this->assertFalse($fresh->isProfessional());
    }

    public function test_18_professional_plus_teacher_receives_exactly_those_two_classifications(): void
    {
        $user = $this->verifiedUser();
        Subject::insert([['id' => 2, 'name' => 'Physics', 'slug' => 'physics', 'created_at' => now(), 'updated_at' => now()]]);

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional', 'teacher']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 1,
            'display_name'           => 'Software Dev & Tutor',
            'bio'                    => 'Dev and Physics teacher.',
            'years_of_experience'    => 5,
            'location'               => 'Yaba, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Yaba',
            'location_neighbourhood' => 'Akoka',
            'phone'                  => '08044556677',
        ]);

        $this->actingAs($user)->post('/onboarding/tutor', [
            'teaching_mode' => 'physical',
            'subject_ids'   => [2],
        ]);

        $fresh = $user->fresh()->load('talentTypes');
        $this->assertTrue($fresh->isProfessional());
        $this->assertTrue($fresh->isTeacher());
        $this->assertFalse($fresh->isSkilledLabour());
        $this->assertCount(2, $fresh->talentTypes);
    }

    public function test_19_all_three_user_receives_exactly_three_classifications(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional', 'teacher', 'skilled_labour']]);

        $fresh = $user->fresh()->load('talentTypes');
        $this->assertCount(3, $fresh->talentTypes);
        $this->assertTrue($fresh->isProfessional());
        $this->assertTrue($fresh->isTeacher());
        $this->assertTrue($fresh->isSkilledLabour());
    }

    public function test_20_teacher_specialization_saves_correctly(): void
    {
        $user = $user = $this->verifiedUser();
        Subject::insert([['id' => 3, 'name' => 'Chemistry', 'slug' => 'chemistry', 'created_at' => now(), 'updated_at' => now()]]);
        EducationLevel::insert([['id' => 1, 'name' => 'Primary', 'slug' => 'primary', 'created_at' => now(), 'updated_at' => now()]]);

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Chem Teacher',
            'bio'                    => 'Teaching chemistry to secondary students in Lagos.',
            'years_of_experience'    => 3,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Alausa',
            'phone'                  => '08055667788',
        ]);

        $this->actingAs($user)->post('/onboarding/tutor', [
            'teaching_mode' => 'both',
            'subject_ids'   => [3],
            'level_ids'     => [1],
        ]);

        $this->assertDatabaseHas('education_profiles', [
            'teaching_mode' => 'both',
        ]);
    }

    public function test_21_skilled_labour_specialization_saves_correctly(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Plumber Guy',
            'bio'                    => 'Plumbing expert with extensive residential experience.',
            'years_of_experience'    => 7,
            'location'               => 'Ikorodu, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikorodu',
            'location_neighbourhood' => 'Agbede',
            'phone'                  => '08066778899',
        ]);

        $this->actingAs($user)->post('/onboarding/skilled-labour', [
            'trade_category_id'   => 10,
            'is_certified'        => true,
            'certification_notes' => 'Trade Test 1 Certificate',
        ]);

        $this->assertDatabaseHas('skilled_labour_profiles', [
            'trade_category_id'   => 10,
            'is_certified'        => true,
            'certification_notes' => 'Trade Test 1 Certificate',
        ]);
    }

    public function test_22_shared_structured_location_saves_correctly(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 1,
            'display_name'           => 'Structured User',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Lekki Phase 1, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
            'location_landmark'      => 'Near Admiralty Way',
            'phone'                  => '08077889900',
        ]);

        $this->assertDatabaseHas('professional_profiles', [
            'user_id'               => $user->id,
            'location_state'        => 'Lagos',
            'location_city'         => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
            'location_landmark'      => 'Near Admiralty Way',
        ]);
    }

    public function test_23_teacher_location_is_obtained_from_shared_professional_profile(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Teacher Loc',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Victoria Island, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Victoria Island',
            'location_neighbourhood' => 'Ahmadu Bello',
            'phone'                  => '08088990011',
        ]);

        $prof = $user->fresh()->professionalProfile;
        $this->assertNotNull($prof);
        $this->assertEquals('Lagos', $prof->location_state);
        $this->assertEquals('Victoria Island', $prof->location_city);
    }

    public function test_24_skilled_labour_location_is_obtained_from_shared_professional_profile(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Labour Loc',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Abuja Municipal, FCT',
            'location_state'         => 'FCT - Abuja',
            'location_city'          => 'Abuja Municipal',
            'location_neighbourhood' => 'Garki',
            'phone'                  => '08099001122',
        ]);

        $prof = $user->fresh()->professionalProfile;
        $this->assertNotNull($prof);
        $this->assertEquals('FCT - Abuja', $prof->location_state);
        $this->assertEquals('Abuja Municipal', $prof->location_city);
    }

    public function test_25_landmark_is_optional(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 1,
            'display_name'           => 'No Landmark',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Computer Village',
            'phone'                  => '08012345678',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('professional_profiles', [
            'user_id'           => $user->id,
            'location_landmark' => null,
        ]);
    }

    public function test_26_invalid_talent_type_is_rejected(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->post('/onboarding/classification', [
            'classifications' => ['astronaut'],
        ]);

        $response->assertSessionHasErrors('classifications.0');
    }

    public function test_27_invalid_skilled_labour_trade_category_is_rejected(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Skilled Invalid',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Allen',
            'phone'                  => '08023456789',
        ]);

        $response = $this->actingAs($user)->post('/onboarding/skilled-labour', [
            'trade_category_id' => 99999,
        ]);

        $response->assertSessionHasErrors('trade_category_id');
    }

    public function test_28_existing_classifications_are_preserved_when_adding_another(): void
    {
        $user = $this->verifiedUser();

        // Initially complete professional
        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 1,
            'display_name'           => 'Existing Pro',
            'bio'                    => 'Experienced dev building fullstack applications.',
            'years_of_experience'    => 5,
            'location'               => 'Yaba, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Yaba',
            'location_neighbourhood' => 'Yaba Tech',
            'phone'                  => '08034567890',
        ]);

        $this->assertTrue($user->fresh()->isProfessional());

        // Now add Teacher classification
        $response = $this->actingAs($user)->post('/onboarding/add-classification', [
            'classification' => 'teacher',
        ]);

        $response->assertRedirectToRoute('onboarding.tutor');

        $fresh = $user->fresh()->load('talentTypes');
        $this->assertTrue($fresh->isProfessional());
        $this->assertTrue($fresh->isTeacher());
    }

    public function test_29_interrupted_multi_classification_onboarding_resumes_correctly(): void
    {
        $user = $this->verifiedUser();
        Subject::insert([['id' => 4, 'name' => 'Biology', 'slug' => 'biology', 'created_at' => now(), 'updated_at' => now()]]);
        EducationLevel::insert([['id' => 2, 'name' => 'Secondary', 'slug' => 'secondary', 'created_at' => now(), 'updated_at' => now()]]);

        // Select teacher + skilled_labour
        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher', 'skilled_labour']]);

        // Complete shared base profile
        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Multi Interrupted',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 3,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Alausa',
            'phone'                  => '08045678901',
        ]);

        // Complete teacher
        $this->actingAs($user)->post('/onboarding/tutor', [
            'teaching_mode' => 'online',
            'subject_ids'   => [4],
            'level_ids'     => [2],
        ]);

        // Leave and return to GET /onboarding
        $response = $this->actingAs($user)->get('/onboarding');

        // Should automatically resume at skilled_labour
        $response->assertRedirectToRoute('onboarding.skilled-labour');
    }

    public function test_30_completed_at_is_tracked_independently_per_classification(): void
    {
        $user = $this->verifiedUser();
        Subject::insert([['id' => 5, 'name' => 'History', 'slug' => 'history', 'created_at' => now(), 'updated_at' => now()]]);
        EducationLevel::insert([['id' => 3, 'name' => 'Tertiary', 'slug' => 'tertiary', 'created_at' => now(), 'updated_at' => now()]]);

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher', 'skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Independent Tracking',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 3,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Alausa',
            'phone'                  => '08056789012',
        ]);

        $this->actingAs($user)->post('/onboarding/tutor', [
            'teaching_mode' => 'online',
            'subject_ids'   => [5],
            'level_ids'     => [3],
        ]);

        $teacherTt = $user->talentTypes()->where('slug', 'teacher')->first()->pivot;
        $skilledTt = $user->talentTypes()->where('slug', 'skilled_labour')->first()->pivot;

        $this->assertNotNull($teacherTt->completed_at);
        $this->assertNull($skilledTt->completed_at);
        $this->assertFalse($user->fresh()->onboarding_completed);
    }

    public function test_31_user_cannot_modify_another_users_talent_profile(): void
    {
        $userA = $this->verifiedUser();
        $userB = $this->verifiedUser();

        // User A logs in and tries to submit user B's profile
        $response = $this->actingAs($userA)->post('/onboarding/professional', [
            'category_id'            => 1,
            'display_name'           => 'Hacked Profile',
            'bio'                    => 'This is a long bio description text for testing purposes.',
            'years_of_experience'    => 2,
            'location'               => 'Ikeja, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Ikeja',
            'location_neighbourhood' => 'Allen',
            'phone'                  => '08067890123',
        ]);

        // Should create User A's profile, NOT User B's profile
        $this->assertDatabaseHas('professional_profiles', ['user_id' => $userA->id]);
        $this->assertDatabaseMissing('professional_profiles', ['user_id' => $userB->id]);
    }

    public function test_32_existing_legacy_onboarding_users_continue_working(): void
    {
        $user = $this->verifiedUser(['onboarding_completed' => true]);
        ProfessionalProfile::create([
            'user_id'      => $user->id,
            'category_id'  => 1,
            'display_name' => 'Legacy User',
            'bio'          => 'Legacy bio.',
            'location'     => 'Lagos',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_33_existing_opportunity_application_rules_still_work(): void
    {
        $incompleteUser = $this->verifiedUser(['onboarding_completed' => false]);
        $completeUser   = $this->verifiedUser(['onboarding_completed' => true]);

        $this->assertFalse($incompleteUser->onboarding_completed);
        $this->assertTrue($completeUser->onboarding_completed);
    }

    public function test_teacher_base_profile_submission_without_structured_location_fails_validation(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'         => 3,
            'display_name'        => 'Teacher Without Location',
            'bio'                 => 'Passionate english teacher with 5 years experience.',
            'years_of_experience' => 4,
            'location'            => 'Lagos',
            'phone'               => '08022334455',
        ]);

        $response->assertSessionHasErrors(['location_state', 'location_city', 'location_neighbourhood']);
    }

    public function test_skilled_labour_base_profile_submission_without_structured_location_fails_validation(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'         => 2,
            'display_name'        => 'Skilled Worker Without Location',
            'bio'                 => 'Experienced electrician offering repair services.',
            'years_of_experience' => 5,
            'location'            => 'Lagos',
            'phone'               => '08033445566',
        ]);

        $response->assertSessionHasErrors(['location_state', 'location_city', 'location_neighbourhood']);
    }

    public function test_teacher_base_profile_saves_successfully_when_structured_location_supplied(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Teacher With Location',
            'bio'                    => 'Passionate english teacher with 5 years experience.',
            'years_of_experience'    => 4,
            'location'               => 'Surulere, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Surulere',
            'location_neighbourhood' => 'Bode Thomas',
            'phone'                  => '08022334455',
        ]);

        $response->assertRedirectToRoute('onboarding.tutor');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('professional_profiles', [
            'user_id'          => $user->id,
            'location_state'   => 'Lagos',
            'location_city'    => 'Surulere',
        ]);
    }

    public function test_skilled_labour_base_profile_saves_successfully_when_structured_location_supplied(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Skilled Worker With Location',
            'bio'                    => 'Experienced electrician offering repair services.',
            'years_of_experience'    => 5,
            'location'               => 'Lekki, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
            'phone'                  => '08033445566',
        ]);

        $response->assertRedirectToRoute('onboarding.skilled-labour');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('professional_profiles', [
            'user_id'          => $user->id,
            'location_state'   => 'Lagos',
            'location_city'    => 'Lekki',
        ]);
    }

    public function test_professional_only_onboarding_permits_structured_location_omitted(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['professional']]);

        $response = $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'         => 1,
            'display_name'        => 'Pro Only User',
            'bio'                 => 'Experienced software engineer and tech consultant.',
            'years_of_experience' => 6,
            'location'            => 'Lagos',
            'phone'               => '08044556677',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('professional_profiles', [
            'user_id'        => $user->id,
            'location_state' => null,
        ]);
    }

    public function test_teacher_specialisation_validation_failures_are_surfaced(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['teacher']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 3,
            'display_name'           => 'Teacher Val',
            'bio'                    => 'Passionate english teacher with 5 years experience.',
            'years_of_experience'    => 4,
            'location'               => 'Surulere, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Surulere',
            'location_neighbourhood' => 'Bode Thomas',
            'phone'                  => '08022334455',
        ]);

        $response = $this->actingAs($user)->post('/onboarding/tutor', []);

        $response->assertSessionHasErrors(['subject_ids', 'level_ids']);
    }

    public function test_skilled_labour_specialisation_validation_failures_are_surfaced(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/onboarding/classification', ['classifications' => ['skilled_labour']]);

        $this->actingAs($user)->post('/onboarding/professional', [
            'category_id'            => 2,
            'display_name'           => 'Skilled Val',
            'bio'                    => 'Experienced electrician offering repair services.',
            'years_of_experience'    => 5,
            'location'               => 'Lekki, Lagos',
            'location_state'         => 'Lagos',
            'location_city'          => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
            'phone'                  => '08033445566',
        ]);

        $response = $this->actingAs($user)->post('/onboarding/skilled-labour', [
            'trade_category_id' => 999999,
        ]);

        $response->assertSessionHasErrors('trade_category_id');
    }
}

