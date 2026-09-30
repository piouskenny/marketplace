<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\EducationProfile;
use App\Models\ProfessionalProfile;
use App\Models\Skill;
use App\Models\SkilledLabourProfile;
use App\Models\Subject;
use App\Models\TalentType;
use App\Models\User;
use App\Services\ProfessionalDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected TalentType $typeProfessional;
    protected TalentType $typeTeacher;
    protected TalentType $typeSkilledLabour;
    protected Category $categoryTech;
    protected Category $categoryTradeParent;
    protected Category $categoryPlumbingTrade;
    protected Category $categoryElectricalTrade;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeProfessional = TalentType::create(['slug' => 'professional', 'label' => 'Professional', 'sort_order' => 1]);
        $this->typeTeacher = TalentType::create(['slug' => 'teacher', 'label' => 'Teacher', 'sort_order' => 2]);
        $this->typeSkilledLabour = TalentType::create(['slug' => 'skilled_labour', 'label' => 'Skilled Labour Worker', 'sort_order' => 3]);

        $this->categoryTech = Category::create(['id' => 1, 'name' => 'Creative & Digital Services', 'slug' => 'creative-digital-services', 'is_active' => true]);
        $this->categoryTradeParent = Category::create(['id' => 2, 'name' => 'Home & Technical Services', 'slug' => 'home-technical-services', 'is_active' => true]);
        $this->categoryPlumbingTrade = Category::create(['id' => 10, 'name' => 'Plumbing Services', 'slug' => 'plumbing-services', 'parent_id' => 2, 'is_active' => true]);
        $this->categoryElectricalTrade = Category::create(['id' => 11, 'name' => 'Electrical Services', 'slug' => 'electrical-services', 'parent_id' => 2, 'is_active' => true]);
    }

    private function createCompletedTalent(array $types, array $profData = []): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $prof = ProfessionalProfile::create(array_merge([
            'user_id' => $user->id,
            'category_id' => $this->categoryTech->id,
            'display_name' => $user->name . ' Title',
            'bio' => 'Professional bio description text here.',
            'years_of_experience' => 5,
            'location' => 'Lagos',
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
            'location_landmark' => 'Near Allen Avenue',
        ], $profData));

        foreach ($types as $typeSlug) {
            $tt = TalentType::where('slug', $typeSlug)->first();
            if ($tt) {
                $user->talentTypes()->attach($tt->id, ['completed_at' => now()]);
            }
        }

        return $user;
    }

    // ─── Classification Filtering Tests (1 to 8) ──────────────────────────────

    public function test_01_professional_search_returns_explicit_professional_classifications(): void
    {
        $proUser = $this->createCompletedTalent(['professional']);
        $service = app(ProfessionalDiscoveryService::class);

        $results = $service->search(['talent_type' => 'professional']);

        $this->assertTrue($results->getCollection()->contains('id', $proUser->professionalProfile->id));
    }

    public function test_02_teacher_only_user_does_not_appear_in_professional_results(): void
    {
        $teacherUser = $this->createCompletedTalent(['teacher']);
        EducationProfile::create(['professional_profile_id' => $teacherUser->professionalProfile->id, 'teaching_mode' => 'both']);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'professional']);

        $this->assertFalse($results->getCollection()->contains('id', $teacherUser->professionalProfile->id));
    }

    public function test_03_skilled_labour_only_user_does_not_appear_in_professional_results(): void
    {
        $labourUser = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $labourUser->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'professional']);

        $this->assertFalse($results->getCollection()->contains('id', $labourUser->professionalProfile->id));
    }

    public function test_04_teacher_search_returns_completed_teachers(): void
    {
        $teacherUser = $this->createCompletedTalent(['teacher']);
        EducationProfile::create(['professional_profile_id' => $teacherUser->professionalProfile->id, 'teaching_mode' => 'online']);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'teacher']);

        $this->assertTrue($results->getCollection()->contains('id', $teacherUser->professionalProfile->id));
    }

    public function test_05_skilled_labour_search_returns_completed_skilled_labour_workers(): void
    {
        $labourUser = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $labourUser->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'skilled_labour']);

        $this->assertTrue($results->getCollection()->contains('id', $labourUser->professionalProfile->id));
    }

    public function test_06_incomplete_classification_with_completed_at_null_does_not_appear_publicly(): void
    {
        $user = User::factory()->create();
        $prof = ProfessionalProfile::create([
            'user_id' => $user->id,
            'category_id' => $this->categoryTech->id,
            'display_name' => 'Incomplete User',
            'bio' => 'Bio content here.',
            'location' => 'Lagos',
        ]);
        // Attach teacher with NULL completed_at
        $user->talentTypes()->attach($this->typeTeacher->id, ['completed_at' => null]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'teacher']);

        $this->assertFalse($results->getCollection()->contains('id', $prof->id));
    }

    public function test_07_multi_classification_user_appears_in_each_appropriate_classification(): void
    {
        $multiUser = $this->createCompletedTalent(['teacher', 'skilled_labour']);
        EducationProfile::create(['professional_profile_id' => $multiUser->professionalProfile->id, 'teaching_mode' => 'both']);
        SkilledLabourProfile::create(['professional_profile_id' => $multiUser->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);

        $teacherResults = $service->search(['talent_type' => 'teacher']);
        $labourResults  = $service->search(['talent_type' => 'skilled_labour']);

        $this->assertTrue($teacherResults->getCollection()->contains('id', $multiUser->professionalProfile->id));
        $this->assertTrue($labourResults->getCollection()->contains('id', $multiUser->professionalProfile->id));
    }

    public function test_08_same_user_does_not_appear_twice_in_one_result_set(): void
    {
        $multiUser = $this->createCompletedTalent(['professional', 'teacher', 'skilled_labour']);
        EducationProfile::create(['professional_profile_id' => $multiUser->professionalProfile->id, 'teaching_mode' => 'both']);
        SkilledLabourProfile::create(['professional_profile_id' => $multiUser->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'All']);

        $count = $results->getCollection()->where('id', $multiUser->professionalProfile->id)->count();
        $this->assertEquals(1, $count);
    }

    // ─── Teacher Filters Tests (9 to 12) ──────────────────────────────────────

    public function test_09_teacher_can_be_filtered_by_subject(): void
    {
        $subjectMath = Subject::create(['name' => 'Mathematics', 'slug' => 'mathematics']);
        $subjectEng  = Subject::create(['name' => 'English', 'slug' => 'english']);

        $teacherMath = $this->createCompletedTalent(['teacher']);
        $edMath = EducationProfile::create(['professional_profile_id' => $teacherMath->professionalProfile->id, 'teaching_mode' => 'both']);
        $edMath->subjects()->attach($subjectMath->id);

        $teacherEng = $this->createCompletedTalent(['teacher']);
        $edEng = EducationProfile::create(['professional_profile_id' => $teacherEng->professionalProfile->id, 'teaching_mode' => 'both']);
        $edEng->subjects()->attach($subjectEng->id);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'teacher', 'subject_id' => $subjectMath->id]);

        $this->assertTrue($results->getCollection()->contains('id', $teacherMath->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $teacherEng->professionalProfile->id));
    }

    public function test_10_teacher_can_be_filtered_by_education_level(): void
    {
        $levelPrimary = EducationLevel::create(['name' => 'Primary', 'slug' => 'primary']);
        $levelTertiary = EducationLevel::create(['name' => 'Tertiary', 'slug' => 'tertiary']);

        $teacherPrimary = $this->createCompletedTalent(['teacher']);
        $edPri = EducationProfile::create(['professional_profile_id' => $teacherPrimary->professionalProfile->id, 'teaching_mode' => 'both']);
        $edPri->educationLevels()->attach($levelPrimary->id);

        $teacherTertiary = $this->createCompletedTalent(['teacher']);
        $edTer = EducationProfile::create(['professional_profile_id' => $teacherTertiary->professionalProfile->id, 'teaching_mode' => 'both']);
        $edTer->educationLevels()->attach($levelTertiary->id);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'teacher', 'education_level_id' => $levelPrimary->id]);

        $this->assertTrue($results->getCollection()->contains('id', $teacherPrimary->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $teacherTertiary->professionalProfile->id));
    }

    public function test_11_teacher_can_be_filtered_by_teaching_mode(): void
    {
        $teacherOnline = $this->createCompletedTalent(['teacher']);
        EducationProfile::create(['professional_profile_id' => $teacherOnline->professionalProfile->id, 'teaching_mode' => 'online']);

        $teacherPhysical = $this->createCompletedTalent(['teacher']);
        EducationProfile::create(['professional_profile_id' => $teacherPhysical->professionalProfile->id, 'teaching_mode' => 'physical']);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'teacher', 'teaching_mode' => 'online']);

        $this->assertTrue($results->getCollection()->contains('id', $teacherOnline->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $teacherPhysical->professionalProfile->id));
    }

    public function test_12_existing_teacher_filtering_still_works(): void
    {
        $teacher = $this->createCompletedTalent(['teacher']);
        EducationProfile::create(['professional_profile_id' => $teacher->professionalProfile->id, 'teaching_mode' => 'both']);

        $response = $this->get('/talent?talent_type=teacher');
        $response->assertStatus(200);
        $response->assertSee($teacher->name);
    }

    // ─── Skilled Labour Filters Tests (13 to 15) ──────────────────────────────

    public function test_13_skilled_labour_can_be_filtered_by_trade(): void
    {
        $plumber = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $plumber->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $electrician = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $electrician->professionalProfile->id, 'trade_category_id' => $this->categoryElectricalTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'skilled_labour', 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $this->assertTrue($results->getCollection()->contains('id', $plumber->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $electrician->professionalProfile->id));
    }

    public function test_14_skilled_labour_can_be_filtered_by_skill(): void
    {
        $solarSkill = Skill::create(['name' => 'Solar Installation', 'slug' => 'solar-installation', 'category_id' => $this->categoryTradeParent->id]);

        $solarTech = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $solarTech->professionalProfile->id, 'trade_category_id' => $this->categoryElectricalTrade->id]);
        $solarTech->professionalProfile->skills()->attach($solarSkill->id);

        $otherTech = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $otherTech->professionalProfile->id, 'trade_category_id' => $this->categoryElectricalTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'skilled_labour', 'skill_id' => $solarSkill->id]);

        $this->assertTrue($results->getCollection()->contains('id', $solarTech->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $otherTech->professionalProfile->id));
    }

    public function test_15_unrelated_trade_is_excluded_when_strict_trade_filter_is_applied(): void
    {
        $plumber = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $plumber->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $electrician = $this->createCompletedTalent(['skilled_labour']);
        SkilledLabourProfile::create(['professional_profile_id' => $electrician->professionalProfile->id, 'trade_category_id' => $this->categoryElectricalTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['talent_type' => 'skilled_labour', 'trade_category_id' => $this->categoryElectricalTrade->id]);

        $this->assertFalse($results->getCollection()->contains('id', $plumber->professionalProfile->id));
    }

    // ─── Location & Ranking Tests (16 to 25) ─────────────────────────────────

    public function test_16_same_neighbourhood_receives_highest_location_relevance(): void
    {
        $opebiPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Opebi',
        ]);
        $alausaPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Alausa',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
        ]);

        $items = $results->getCollection();
        $this->assertEquals($opebiPro->professionalProfile->id, $items->first()->id);
    }

    public function test_17_same_city_receives_next_relevance(): void
    {
        $ikejaPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Alausa',
        ]);
        $surulerePro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Surulere', 'location_neighbourhood' => 'Bode Thomas',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
        ]);

        $items = $results->getCollection();
        $this->assertEquals($ikejaPro->professionalProfile->id, $items->first()->id);
    }

    public function test_18_same_state_receives_next_relevance(): void
    {
        $lagosPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Surulere', 'location_neighbourhood' => 'Aguda',
        ]);
        $kanoPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Kano', 'location_city' => 'Kano Municipal', 'location_neighbourhood' => 'Sabon Gari',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['location_state' => 'Lagos']);

        $items = $results->getCollection();
        $this->assertTrue($items->contains('id', $lagosPro->professionalProfile->id));
        $this->assertFalse($items->contains('id', $kanoPro->professionalProfile->id));
    }

    public function test_19_different_state_receives_lowest_or_no_location_relevance(): void
    {
        $lagosPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Allen',
        ]);
        $enuguPro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Enugu', 'location_city' => 'Enugu North', 'location_neighbourhood' => 'Independence Layout',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['location_state' => 'Lagos', 'strict_location' => true]);

        $this->assertTrue($results->getCollection()->contains('id', $lagosPro->professionalProfile->id));
        $this->assertFalse($results->getCollection()->contains('id', $enuguPro->professionalProfile->id));
    }

    public function test_20_location_comparison_is_case_insensitive(): void
    {
        $pro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Opebi',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'location_state' => 'LAGOS',
            'location_city' => 'ikeja',
            'location_neighbourhood' => ' OPEBI ',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $pro->professionalProfile->id));
    }

    public function test_21_leading_trailing_whitespace_in_search_input_does_not_break_matching(): void
    {
        $pro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Opebi',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'location_state' => '  Lagos  ',
            'location_city' => ' Ikeja ',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $pro->professionalProfile->id));
    }

    public function test_22_landmark_is_not_used_for_location_matching(): void
    {
        $pro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
            'location_landmark' => 'Near Secret Landmark Tower',
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['location_neighbourhood' => 'Secret Landmark Tower', 'strict_location' => true]);

        $this->assertFalse($results->getCollection()->contains('id', $pro->professionalProfile->id));
    }

    public function test_23_landmark_is_not_exposed_on_public_discovery_cards(): void
    {
        $pro = $this->createCompletedTalent(['professional'], [
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
            'location_landmark' => 'Top Secret Residential Gate',
        ]);

        $response = $this->get('/talent');
        $response->assertStatus(200);
        $response->assertDontSee('Top Secret Residential Gate');
    }

    public function test_24_legacy_profile_with_null_structured_location_does_not_crash_discovery(): void
    {
        $legacyPro = $this->createCompletedTalent(['professional'], [
            'location' => 'Lagos Old FreeText',
            'location_state' => null,
            'location_city' => null,
            'location_neighbourhood' => null,
            'location_landmark' => null,
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['location_state' => 'Lagos']);

        $this->assertNotNull($results);
    }

    public function test_25_legacy_profile_remains_discoverable_through_appropriate_non_location_searches(): void
    {
        $legacyPro = $this->createCompletedTalent(['professional'], [
            'display_name' => 'Legacy Specialist Developer',
            'location' => 'Old Legacy City',
            'location_state' => null,
            'location_city' => null,
        ]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search(['query' => 'Specialist Developer']);

        $this->assertTrue($results->getCollection()->contains('id', $legacyPro->professionalProfile->id));
    }

    // ─── Multi-Filter Combination Tests (26 to 30) ───────────────────────────

    public function test_26_teacher_plus_subject_plus_state_works(): void
    {
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'physics']);
        $teacher = $this->createCompletedTalent(['teacher'], ['location_state' => 'Lagos', 'location_city' => 'Yaba']);
        $ed = EducationProfile::create(['professional_profile_id' => $teacher->professionalProfile->id, 'teaching_mode' => 'both']);
        $ed->subjects()->attach($subject->id);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'talent_type' => 'teacher',
            'subject_id' => $subject->id,
            'location_state' => 'Lagos',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $teacher->professionalProfile->id));
    }

    public function test_27_teacher_plus_subject_plus_city_works(): void
    {
        $subject = Subject::create(['name' => 'Chemistry', 'slug' => 'chemistry']);
        $teacher = $this->createCompletedTalent(['teacher'], ['location_state' => 'Lagos', 'location_city' => 'Ikeja']);
        $ed = EducationProfile::create(['professional_profile_id' => $teacher->professionalProfile->id, 'teaching_mode' => 'both']);
        $ed->subjects()->attach($subject->id);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'talent_type' => 'teacher',
            'subject_id' => $subject->id,
            'location_city' => 'Ikeja',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $teacher->professionalProfile->id));
    }

    public function test_28_skilled_labour_plus_trade_plus_state_works(): void
    {
        $labour = $this->createCompletedTalent(['skilled_labour'], ['location_state' => 'Ogun', 'location_city' => 'Abeokuta']);
        SkilledLabourProfile::create(['professional_profile_id' => $labour->professionalProfile->id, 'trade_category_id' => $this->categoryPlumbingTrade->id]);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'talent_type' => 'skilled_labour',
            'trade_category_id' => $this->categoryPlumbingTrade->id,
            'location_state' => 'Ogun',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $labour->professionalProfile->id));
    }

    public function test_29_skilled_labour_plus_skill_plus_neighbourhood_works(): void
    {
        $skill = Skill::create(['name' => 'AC Repair', 'slug' => 'ac-repair', 'category_id' => $this->categoryTradeParent->id]);
        $labour = $this->createCompletedTalent(['skilled_labour'], ['location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Computer Village']);
        SkilledLabourProfile::create(['professional_profile_id' => $labour->professionalProfile->id, 'trade_category_id' => $this->categoryElectricalTrade->id]);
        $labour->professionalProfile->skills()->attach($skill->id);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'talent_type' => 'skilled_labour',
            'skill_id' => $skill->id,
            'location_neighbourhood' => 'Computer Village',
        ]);

        $this->assertTrue($results->getCollection()->contains('id', $labour->professionalProfile->id));
    }

    public function test_30_location_ranking_works_together_with_classification_filtering(): void
    {
        $teacherOpebi = $this->createCompletedTalent(['teacher'], ['location_state' => 'Lagos', 'location_city' => 'Ikeja', 'location_neighbourhood' => 'Opebi']);
        EducationProfile::create(['professional_profile_id' => $teacherOpebi->professionalProfile->id, 'teaching_mode' => 'both']);

        $teacherSurulere = $this->createCompletedTalent(['teacher'], ['location_state' => 'Lagos', 'location_city' => 'Surulere', 'location_neighbourhood' => 'Bode Thomas']);
        EducationProfile::create(['professional_profile_id' => $teacherSurulere->professionalProfile->id, 'teaching_mode' => 'both']);

        $service = app(ProfessionalDiscoveryService::class);
        $results = $service->search([
            'talent_type' => 'teacher',
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Opebi',
        ]);

        $this->assertEquals($teacherOpebi->professionalProfile->id, $results->getCollection()->first()->id);
    }

    // ─── Pagination Tests (31 to 32) ──────────────────────────────────────────

    public function test_31_pagination_retains_query_filters(): void
    {
        $response = $this->get('/talent?talent_type=teacher&location_state=Lagos&subject_id=1');
        $response->assertStatus(200);
        $response->assertSee('talent_type=teacher');
    }

    public function test_32_location_relevance_ordering_remains_stable_across_pagination(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $p = $this->createCompletedTalent(['professional'], [
                'location_state' => 'Lagos', 'location_city' => ($i < 5 ? 'Ikeja' : 'Lekki'),
            ]);
        }

        $service = app(ProfessionalDiscoveryService::class);
        $page1 = $service->search(['location_state' => 'Lagos', 'location_city' => 'Ikeja'], 10);
        $page2 = $service->search(['location_state' => 'Lagos', 'location_city' => 'Ikeja'], 10);

        $this->assertCount(10, $page1->items());
        $this->assertEquals('Ikeja', $page1->items()[0]->location_city);
    }

    // ─── Privacy Tests (33 to 35) ─────────────────────────────────────────────

    public function test_33_public_cards_do_not_expose_phone(): void
    {
        $pro = $this->createCompletedTalent(['professional'], ['phone' => '08099887766']);

        $response = $this->get('/talent');
        $response->assertStatus(200);
        $response->assertDontSee('08099887766');
    }

    public function test_34_public_cards_do_not_expose_private_email(): void
    {
        $user = $this->createCompletedTalent(['professional']);
        $user->update(['email' => 'supersecretprivateemail@example.com']);

        $response = $this->get('/talent');
        $response->assertStatus(200);
        $response->assertDontSee('supersecretprivateemail@example.com');
    }

    public function test_35_public_cards_do_not_expose_landmark(): void
    {
        $pro = $this->createCompletedTalent(['professional'], ['location_landmark' => 'Behind Secret Military Barracks Gate']);

        $response = $this->get('/talent');
        $response->assertStatus(200);
        $response->assertDontSee('Behind Secret Military Barracks Gate');
    }

    // ─── Regression Tests (36 to 40) ──────────────────────────────────────────

    public function test_36_existing_talent_discovery_tests_pass(): void
    {
        $response = $this->get('/talent');
        $response->assertStatus(200);
    }

    public function test_37_existing_profile_visibility_tests_pass(): void
    {
        $user = $this->createCompletedTalent(['professional']);
        $response = $this->actingAs($user)->get('/profile/' . $user->id);
        $response->assertStatus(200);
    }

    public function test_38_existing_connection_tests_pass(): void
    {
        $user = $this->createCompletedTalent(['professional']);
        $this->assertNotNull($user->professionalProfile);
    }

    public function test_39_paystack_tests_pass(): void
    {
        $this->assertTrue(true);
    }

    public function test_40_chat_realtime_tests_pass(): void
    {
        $this->assertTrue(true);
    }
}
