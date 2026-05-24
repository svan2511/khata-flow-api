<?php

namespace Tests\Feature;

use App\Enums\OtpPurpose;
use App\Models\Otp;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private string $phone = '9999999999';

    private string $otpCode = '123456';

    protected function setUp(): void
    {
        parent::setUp();

        $clientRepository = app(ClientRepository::class);
        $clientRepository->createPersonalAccessGrantClient(
            'Test Personal Access Client', 'users'
        );

        $this->mockCloudinary();
    }

    private function mockCloudinary(): void
    {
        $this->mock(CloudinaryService::class, function ($mock) {
            $mock->shouldReceive('upload')
                ->andReturn([
                    'url' => 'https://res.cloudinary.com/test/image/upload/v1/test.jpg',
                    'public_id' => 'test/public_id',
                    'format' => 'jpg',
                    'size' => 1024,
                ]);

            $mock->shouldReceive('delete')
                ->andReturn(true);
        });
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'phone' => $this->phone,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'phone',
                    'has_shop',
                ],
            ]);

        $this->assertDatabaseHas('users', ['phone' => $this->phone]);
    }

    public function test_registration_requires_valid_phone(): void
    {
        $response = $this->postJson('/api/register', [
            'phone' => 'invalid',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_verify_otp(): void
    {
        User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => null]);

        Otp::create([
            'phone' => $this->phone,
            'otp' => $this->otpCode,
            'purpose' => OtpPurpose::Registration->value,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'phone' => $this->phone,
            'otp' => $this->otpCode,
            'purpose' => OtpPurpose::Registration->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'phone', 'has_shop'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertNotNull($response->json('data.token'));
    }

    public function test_otp_verification_fails_with_wrong_otp(): void
    {
        User::factory()->create(['phone' => $this->phone]);

        $response = $this->postJson('/api/verify-otp', [
            'phone' => $this->phone,
            'otp' => '000000',
            'purpose' => OtpPurpose::Registration->value,
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_setup_shop(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Passport::actingAs($user);

        $response = $this->postJson('/api/shop/setup', [
            'shop_name' => 'Test General Store',
            'owner_name' => 'Test Owner',
            'phone' => $this->phone,
            'email' => 'shop@test.com',
            'address' => '123 Test Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'gstin' => '27ABCDE1234F1Z5',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'shop_name',
                    'shop_slug',
                    'owner_name',
                    'phone',
                    'city',
                    'state',
                ],
            ]);

        $this->assertDatabaseHas('shops', ['shop_name' => 'Test General Store']);
    }

    public function test_shop_setup_requires_auth(): void
    {
        $response = $this->postJson('/api/shop/setup', [
            'shop_name' => 'Test Store',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_view_dashboard(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        Product::factory()->count(3)->lowStock()->create(['shop_id' => $shop->id]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'today_sales',
                    'total_credit',
                    'low_stock_count',
                    'today_bills_count',
                    'has_shop',
                ],
            ]);
    }

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }

    public function test_user_can_view_profile(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->getJson('/api/user/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'phone', 'email'],
                    'shop' => ['id', 'shop_name', 'shop_slug'],
                ],
            ]);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->putJson('/api/user/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'shop_name' => 'Updated Store Name',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', ['name' => 'Updated Name', 'email' => 'updated@example.com']);
        $this->assertDatabaseHas('shops', ['shop_name' => 'Updated Store Name']);
    }

    public function test_user_can_view_sync_status(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->getJson('/api/sync/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'is_synced',
                    'pending_count',
                    'last_synced_at',
                ],
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Passport::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
    }
}
