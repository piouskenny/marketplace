<?php

namespace Tests\Feature;

use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\ProfessionalProfile;
use App\Models\SkilledLabourProfile;
use App\Models\TalentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Education & Tutoring',
            'slug' => 'education-tutoring',
        ]);
    }

    public function test_user_can_update_profile_information_without_uploading_avatar()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'phone' => '+2348000000000',
            'location' => 'Lagos',
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Updated Name',
            'phone' => '+2348111111111',
            'location' => 'Abuja',
            'category_id' => $this->category->id,
            'display_name' => 'Updated Display Name',
            'bio' => 'Updated Bio text',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '+2348111111111',
            'location' => 'Abuja',
        ]);
    }

    public function test_user_can_upload_avatar()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Avatar User',
            'category_id' => $this->category->id,
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Profile updated successfully.');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_avatar_path_is_persisted_correctly()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $file = UploadedFile::fake()->create('my-photo.png', 100, 'image/png');

        $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Persisted Avatar User',
            'category_id' => $this->category->id,
            'avatar' => $file,
        ]);

        $user->refresh();
        $this->assertStringStartsWith('avatars/', $user->avatar);
        $this->assertEquals(asset('storage/' . $user->avatar), $user->avatar_url);
    }

    public function test_replacing_avatar_removes_previous_avatar_only_after_successful_replacement()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('old-avatar.jpg', 100, 'image/jpeg');
        $oldPath = $oldFile->store('avatars', 'public');

        $user = User::factory()->create([
            'avatar' => $oldPath,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->create('new-avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Replacing Avatar User',
            'category_id' => $this->category->id,
            'avatar' => $newFile,
        ]);

        $response->assertRedirect();
        $user->refresh();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_invalid_avatar_produces_visible_validation_feedback()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Invalid Avatar User',
            'category_id' => $this->category->id,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        
        $editResponse = $this->actingAs($user)->get('/profile/edit');
        $editResponse->assertSee('The avatar field must be an image.');
    }

    public function test_existing_structured_location_survives_profile_settings_update()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        ProfessionalProfile::create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'display_name' => 'Math Teacher',
            'bio' => 'Experienced tutor.',
            'location' => 'Ikeja, Lagos',
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Allen Avenue',
            'location_landmark' => 'Near Ikeja Bus Stop',
        ]);

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Teacher User Updated',
            'phone' => '+2348033333333',
            'location' => 'Ikeja, Lagos',
            'category_id' => $this->category->id,
            'display_name' => 'Math Teacher Updated',
            'bio' => 'Updated bio content.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('professional_profiles', [
            'user_id' => $user->id,
            'location_state' => 'Lagos',
            'location_city' => 'Ikeja',
            'location_neighbourhood' => 'Allen Avenue',
            'location_landmark' => 'Near Ikeja Bus Stop',
        ]);
    }

    public function test_teacher_only_user_does_not_gain_professional_classification_from_settings_update()
    {
        $teacherType = TalentType::create([
            'slug' => TalentClassification::Teacher->value,
            'label' => 'Teacher & Academic Tutor',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $user->talentTypes()->attach($teacherType->id, ['completed_at' => now()]);

        $this->assertTrue($user->isTeacher());
        $this->assertFalse($user->isProfessional());

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Teacher Only',
            'category_id' => $this->category->id,
            'display_name' => 'Tutor Title',
            'bio' => 'Tutor Bio',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertTrue($user->isTeacher());
        $this->assertFalse($user->isProfessional());
    }

    public function test_skilled_labour_only_user_does_not_gain_professional_classification_from_settings_update()
    {
        $skilledLabourType = TalentType::create([
            'slug' => TalentClassification::SkilledLabour->value,
            'label' => 'Skilled Labour Worker',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $user->talentTypes()->attach($skilledLabourType->id, ['completed_at' => now()]);

        $this->assertTrue($user->isSkilledLabour());
        $this->assertFalse($user->isProfessional());

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Skilled Worker',
            'category_id' => $this->category->id,
            'display_name' => 'Plumber Title',
            'bio' => 'Plumbing bio',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertTrue($user->isSkilledLabour());
        $this->assertFalse($user->isProfessional());
    }

    public function test_customer_non_provider_does_not_gain_professional_classification_from_settings_update()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $this->assertFalse($user->isAnyTalent());

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Pure Hirer',
            'phone' => '+2348099999999',
            'location' => 'Abuja',
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->isProfessional());
        $this->assertFalse($user->isAnyTalent());
    }

    public function test_successful_update_produces_expected_success_flash_message()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($user)->put('/profile/edit', [
            'name' => 'Flash Message User',
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Profile updated successfully.');
    }
}
