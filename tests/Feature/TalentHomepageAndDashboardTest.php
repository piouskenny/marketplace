<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EducationProfile;
use App\Models\ProfessionalProfile;
use App\Models\SkilledLabourProfile;
use App\Models\Subject;
use App\Models\TalentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentHomepageAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\TalentTypeSeeder::class);
        $this->defaultCategory = Category::create(['name' => 'General Services', 'slug' => 'general-services']);
    }

    private function createPro(User $user, array $attrs = []): ProfessionalProfile
    {
        return ProfessionalProfile::create(array_merge([
            'user_id' => $user->id,
            'category_id' => $this->defaultCategory->id,
            'display_name' => $user->name,
        ], $attrs));
    }

    public function test_1_homepage_renders_skilled_labour_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Skilled Labour Workers');
    }

    public function test_2_homepage_renders_teacher_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Find Teachers');
    }

    public function test_3_incomplete_skilled_labour_does_not_appear(): void
    {
        $user = User::factory()->create(['name' => 'Incomplete Artisan']);
        $pro = $this->createPro($user);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);
        
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $user->talentTypes()->attach($slType->id, ['completed_at' => null]);

        $response = $this->get('/');
        $response->assertDontSee('Incomplete Artisan');
    }

    public function test_4_incomplete_teacher_does_not_appear(): void
    {
        $user = User::factory()->create(['name' => 'Incomplete Educator']);
        $pro = $this->createPro($user);
        EducationProfile::create(['professional_profile_id' => $pro->id]);
        
        $teacherType = TalentType::where('slug', 'teacher')->first();
        $user->talentTypes()->attach($teacherType->id, ['completed_at' => null]);

        $response = $this->get('/');
        $response->assertDontSee('Incomplete Educator');
    }

    public function test_5_completed_skilled_labour_appears(): void
    {
        $user = User::factory()->create(['name' => 'Completed Artisan']);
        $pro = $this->createPro($user, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);

        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $user->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->get('/');
        $response->assertSee('Completed Artisan');
    }

    public function test_6_completed_teacher_appears(): void
    {
        $user = User::factory()->create(['name' => 'Completed Educator']);
        $pro = $this->createPro($user, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
        ]);
        EducationProfile::create(['professional_profile_id' => $pro->id]);

        $teacherType = TalentType::where('slug', 'teacher')->first();
        $user->talentTypes()->attach($teacherType->id, ['completed_at' => now()]);

        $response = $this->get('/');
        $response->assertSee('Completed Educator');
    }

    public function test_7_multi_classification_user_can_appear_in_both(): void
    {
        $user = User::factory()->create(['name' => 'Multi Talent User']);
        $pro = $this->createPro($user, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
        ]);
        EducationProfile::create(['professional_profile_id' => $pro->id]);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);

        $teacherType = TalentType::where('slug', 'teacher')->first();
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        
        $user->talentTypes()->attach($teacherType->id, ['completed_at' => now()]);
        $user->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->get('/');
        $response->assertSee('Multi Talent User');
    }

    public function test_8_9_10_public_cards_do_not_expose_phone_email_or_landmark(): void
    {
        $user = User::factory()->create([
            'name' => 'Private Info Provider',
            'phone' => '08099998888',
            'email' => 'privateprovider@secret.com',
        ]);
        $pro = $this->createPro($user, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_landmark' => 'Behind Secret Building 123',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);
        
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $user->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->get('/');
        $response->assertSee('Private Info Provider');
        $response->assertDontSee('08099998888');
        $response->assertDontSee('privateprovider@secret.com');
        $response->assertDontSee('Behind Secret Building 123');
    }

    public function test_11_skilled_labour_view_all_links_to_correct_filter(): void
    {
        $response = $this->get('/');
        $response->assertSee(route('talent.index', ['talent_type' => 'skilled_labour']));
    }

    public function test_12_teacher_view_all_links_correctly(): void
    {
        $response = $this->get('/');
        $response->assertSee(route('talent.index', ['talent_type' => 'teacher']));
    }

    public function test_13_trade_shortcut_applies_correct_filter(): void
    {
        $parentCat = Category::create(['name' => 'Home Maintenance', 'slug' => 'home-maintenance']);
        $trade = Category::create(['name' => 'Electrician Trade', 'slug' => 'electrician-trade', 'parent_id' => $parentCat->id]);

        $response = $this->get('/');
        $response->assertSee(route('talent.index', ['talent_type' => 'skilled_labour', 'trade_category_id' => $trade->id]));
    }

    public function test_14_subject_shortcut_applies_correct_filter(): void
    {
        $subject = Subject::create(['name' => 'Advanced Robotics', 'slug' => 'advanced-robotics']);

        $response = $this->get('/');
        $response->assertSee(route('talent.index', ['talent_type' => 'teacher', 'subject_id' => $subject->id]));
    }

    public function test_15_guest_without_selected_location_does_not_receive_falsely_labelled_near_you(): void
    {
        $response = $this->get('/');
        $response->assertSee('Skilled Labour Workers');
        $response->assertDontSee('Skilled Labour Workers Near Ikeja');
    }

    public function test_16_dashboard_renders_both_discovery_sections(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Find Skilled Labour Workers Near You');
        $response->assertSee('Find Teachers Near You');
    }

    public function test_17_to_20_dashboard_location_ranking(): void
    {
        $viewer = User::factory()->create();
        $this->createPro($viewer, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
        ]);

        // Provider A: Same neighbourhood (Opebi)
        $userA = User::factory()->create(['name' => 'Talent Opebi']);
        $proA = $this->createPro($userA, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $proA->id]);
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $userA->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        // Provider B: Same city (Ikeja, Allen)
        $userB = User::factory()->create(['name' => 'Talent Allen']);
        $proB = $this->createPro($userB, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Allen',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $proB->id]);
        $userB->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        // Provider C: Same state (Lekki, Lagos)
        $userC = User::factory()->create(['name' => 'Talent Lekki']);
        $proC = $this->createPro($userC, [
            'location_state' => 'Lagos',
            'location_city' => 'Lekki',
            'location_neighbourhood' => 'Phase 1',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $proC->id]);
        $userC->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->actingAs($viewer)->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();
        $posA = strpos($content, 'Talent Opebi');
        $posB = strpos($content, 'Talent Allen');
        $posC = strpos($content, 'Talent Lekki');

        $this::assertNotFalse($posA);
        $this::assertNotFalse($posB);
        $this::assertNotFalse($posC);

        // Assert ranking order: Opebi before Allen, Allen before Lekki
        $this::assertTrue($posA < $posB, 'Same neighbourhood should rank before same city');
        $this::assertTrue($posB < $posC, 'Same city should rank before same state');
    }

    public function test_21_customer_without_professional_profile_does_not_cause_error(): void
    {
        $customer = User::factory()->create(); // No ProfessionalProfile

        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Find Skilled Labour Workers Near You');
    }

    public function test_22_customer_without_location_sees_location_selection_state(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Set Search Location');
    }

    public function test_23_24_25_customer_can_select_search_location_without_becoming_talent(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)->get('/dashboard?location_state=Lagos&location_city=Ikeja');
        $response->assertStatus(200);
        $response->assertSee('Ikeja');

        // Verify customer account remains unchanged (no ProfessionalProfile, no talent_type_user)
        $this::assertNull($customer->fresh()->professionalProfile);
        $this::assertEquals(0, $customer->fresh()->talentTypes()->count());
    }

    public function test_26_dashboard_cards_do_not_expose_private_contact_information(): void
    {
        $viewer = User::factory()->create();

        $talent = User::factory()->create([
            'name' => 'Secret Artisan',
            'phone' => '07011112222',
            'email' => 'secretartisan@domain.com',
        ]);
        $pro = $this->createPro($talent, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_landmark' => 'Near Central Landmark',
        ]);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $talent->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->actingAs($viewer)->get('/dashboard');
        $response->assertSee('Secret Artisan');
        $response->assertDontSee('07011112222');
        $response->assertDontSee('secretartisan@domain.com');
        $response->assertDontSee('Near Central Landmark');
    }

    public function test_27_multi_classification_talent_appears_correctly_on_dashboard(): void
    {
        $viewer = User::factory()->create();

        $multi = User::factory()->create(['name' => 'Dual Specialist']);
        $pro = $this->createPro($multi, [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
        ]);
        EducationProfile::create(['professional_profile_id' => $pro->id]);
        SkilledLabourProfile::create(['professional_profile_id' => $pro->id]);

        $teacherType = TalentType::where('slug', 'teacher')->first();
        $slType = TalentType::where('slug', 'skilled_labour')->first();
        $multi->talentTypes()->attach($teacherType->id, ['completed_at' => now()]);
        $multi->talentTypes()->attach($slType->id, ['completed_at' => now()]);

        $response = $this->actingAs($viewer)->get('/dashboard');
        $response->assertSee('Dual Specialist');
    }

    public function test_28_dashboard_view_all_links_preserve_filters(): void
    {
        $viewer = User::factory()->create();

        $response = $this->actingAs($viewer)->get('/dashboard');
        $response->assertSee(route('talent.index', ['talent_type' => 'skilled_labour']));
        $response->assertSee(route('talent.index', ['talent_type' => 'teacher']));
    }
}
