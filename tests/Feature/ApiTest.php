<?php

namespace Tests\Feature;

use App\Enums\OtpPurpose;
use App\Models\Customer;
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

    private string $otpCode = '1234';

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
            'otp' => '0000',
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
                    'today_credit',
                    'today_cash',
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

    public function test_daily_report_includes_expenses_and_profit(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $billResponse = $this->postJson('/api/bills', [
            'items' => [
                ['product_name' => 'Test Item', 'unit_price' => 100, 'quantity' => 2],
            ],
            'payment_method' => 'cash',
            'paid_amount' => 200,
        ]);
        $billResponse->assertStatus(201);

        $expenseResponse = $this->postJson('/api/expenses', [
            'title' => 'Electricity',
            'amount' => 50,
            'expense_date' => today()->toDateString(),
        ]);
        $expenseResponse->assertStatus(201);

        $response = $this->getJson('/api/reports/daily');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_sales', 200)
            ->assertJsonPath('data.total_expenses', 50)
            ->assertJsonPath('data.net_profit', 150);
    }

    public function test_user_can_update_customer(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['shop_id' => $shop->id]);
        Passport::actingAs($user);

        $response = $this->putJson('/api/customers/'.$customer->uuid, [
            'name' => 'Updated Customer',
            'phone' => '9876543210',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated Customer')
            ->assertJsonPath('data.phone', '9876543210');

        $this->assertDatabaseHas('customers', ['uuid' => $customer->uuid, 'name' => 'Updated Customer']);
    }

    public function test_customer_update_accepts_post_method(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['shop_id' => $shop->id]);
        Passport::actingAs($user);

        $response = $this->postJson('/api/customers/'.$customer->uuid, [
            'name' => 'Post Updated',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Post Updated');
    }

    public function test_customer_update_validation_returns_consistent_json(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['shop_id' => $shop->id]);
        Passport::actingAs($user);

        $response = $this->putJson('/api/customers/'.$customer->uuid, [
            'name' => '',
            'phone' => 'bad!!',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function test_customer_update_unknown_id_returns_json_404(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Shop::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->putJson('/api/customers/00000000-0000-0000-0000-000000000000', [
            'name' => 'Ghost',
        ]);

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_customer_update_rejects_duplicate_phone_with_friendly_message(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        Customer::factory()->create(['shop_id' => $shop->id, 'phone' => '9876543210']);
        $other = Customer::factory()->create(['shop_id' => $shop->id, 'phone' => '9123456780']);
        Passport::actingAs($user);

        $response = $this->putJson('/api/customers/'.$other->uuid, [
            'name' => 'Other Customer',
            'phone' => '9876543210',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Another customer with this phone number already exists.');
    }

    public function test_product_search_is_case_insensitive(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'White Sauce Pasta',
        ]);
        Passport::actingAs($user);

        foreach (['white sauce pasta', 'WHITE SAUCE PASTA', 'White Sauce Pasta'] as $term) {
            $this->getJson('/api/products/search?q='.urlencode($term).'&limit=10')
                ->assertStatus(200)
                ->assertJsonPath('data.0.id', $product->uuid)
                ->assertJsonPath('data.0.name', 'White Sauce Pasta');
        }
    }

    public function test_inventory_product_list_search_is_case_insensitive(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'White Sauce Pasta',
        ]);
        Passport::actingAs($user);

        $this->getJson('/api/products?search='.urlencode('white sauce pasta'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $product->uuid)
            ->assertJsonPath('data.0.name', 'White Sauce Pasta');
    }

    public function test_customer_search_is_case_insensitive(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Ramesh Kumar',
            'phone' => '9876543210',
        ]);
        Passport::actingAs($user);

        foreach (['ramesh', 'RAMESH', 'Ramesh Kumar'] as $term) {
            $this->getJson('/api/customers/search?q='.urlencode($term).'&limit=10')
                ->assertStatus(200)
                ->assertJsonPath('data.0.id', $customer->uuid)
                ->assertJsonPath('data.0.name', 'Ramesh Kumar');
        }
    }

    public function test_unknown_api_route_returns_json_not_html(): void
    {
        $user = User::factory()->create(['phone' => $this->phone, 'phone_verified_at' => now()]);
        Passport::actingAs($user);

        $response = $this->getJson('/api/does-not-exist');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }
}
