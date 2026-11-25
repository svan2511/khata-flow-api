<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class VoiceParseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient(
            'Test Personal Access Client', 'users'
        );

        config([
            'services.gemini.key' => 'test-key',
            'services.gemini.model' => 'gemini-3.5-flash-lite',
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ]);
    }

    /**
     * @return array{0: User, 1: Shop}
     */
    private function makeShop(): array
    {
        $user = User::factory()->create([
            'phone' => '9999999999',
            'phone_verified_at' => now(),
        ]);

        $shop = Shop::factory()->create(['user_id' => $user->id]);

        return [$user, $shop];
    }

    private function fakeGemini(array $draft): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => json_encode($draft)]],
                    ],
                ]],
            ]),
        ]);
    }

    private function draft(array $overrides = []): array
    {
        return array_merge([
            'items' => [],
            'customer_name' => '',
            'payment_method' => 'cash',
            'discount_type' => 'none',
            'discount_value' => 0,
            'notes' => '',
            'assistant_reply' => 'Bill taiyaar hai.',
        ], $overrides);
    }

    public function test_voice_parse_requires_authentication(): void
    {
        $this->postJson('/api/voice/parse', ['transcript' => 'do kilo aata'])
            ->assertStatus(401);
    }

    public function test_voice_parse_validates_transcript(): void
    {
        [$user] = $this->makeShop();
        Passport::actingAs($user);

        $this->postJson('/api/voice/parse', ['transcript' => ''])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_voice_parse_returns_catalog_mapped_draft(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $atta = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Aashirvaad Atta 5kg',
            'price' => 250,
            'unit' => 'kg',
        ]);

        $maggi = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Maggi Noodles',
            'price' => 14,
            'unit' => 'piece',
        ]);

        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => $atta->uuid, 'spoken_name' => 'aata', 'quantity' => 2, 'unit' => 'kilo', 'confidence' => 0.95],
                ['product_uuid' => $maggi->uuid, 'spoken_name' => 'maggi', 'quantity' => 3, 'unit' => 'packet', 'confidence' => 0.9],
            ],
            'customer_name' => 'Ramesh',
            'payment_method' => 'credit',
            'assistant_reply' => 'Do kilo atta aur teen Maggi udhaar pe likh diya.',
        ]));

        $response = $this->postJson('/api/voice/parse', [
            'transcript' => 'Ramesh ko do kilo aata aur teen maggi udhaar',
            'language' => 'hi',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.items.0.product_uuid', $atta->uuid)
            ->assertJsonPath('data.items.0.name', 'Aashirvaad Atta 5kg')
            ->assertJsonPath('data.items.0.resolved', true)
            ->assertJsonPath('data.items.0.unit', 'kg')
            ->assertJsonPath('data.items.1.product_uuid', $maggi->uuid)
            ->assertJsonPath('data.items.1.quantity', 3)
            ->assertJsonPath('data.payment_method', 'credit')
            ->assertJsonPath('data.customer_name', 'Ramesh')
            ->assertJsonPath('data.source', 'gemini');

        $this->assertEquals(250.0, $response->json('data.items.0.price'));
        $this->assertEquals(2, $response->json('data.items.0.quantity'));

        // Voice parsing must never create a bill on its own.
        $this->assertDatabaseCount('bills', 0);

        Http::assertSent(function (Request $request) use ($atta) {
            $body = $request->body();

            return str_contains($request->url(), 'generateContent')
                && $request->hasHeader('x-goog-api-key', 'test-key')
                && str_contains($body, $atta->uuid)
                && str_contains($body, 'responseJsonSchema');
        });
    }

    public function test_voice_parse_flags_unknown_products_for_review(): void
    {
        [$user] = $this->makeShop();
        Passport::actingAs($user);

        $this->fakeGemini($this->draft([
            'items' => [
                [
                    'product_uuid' => '00000000-0000-0000-0000-000000000000',
                    'spoken_name' => 'gud',
                    'quantity' => 1,
                    'unit' => 'kg',
                    'confidence' => 0.2,
                ],
            ],
        ]));

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'ek kilo gud']);

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.product_uuid', null)
            ->assertJsonPath('data.items.0.name', 'gud')
            ->assertJsonPath('data.items.0.resolved', false)
            ->assertJsonPath('data.items.0.unit', 'kg');

        $this->assertEquals(0, $response->json('data.items.0.price'));
    }

    public function test_voice_parse_resolves_spoken_alias_when_gemini_leaves_item_unmatched(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $tea = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Tea',
            'price' => 20,
            'unit' => 'plate',
        ]);

        Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Masala Tea',
            'price' => 30,
            'unit' => 'plate',
        ]);

        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => '', 'spoken_name' => 'chai', 'quantity' => 2, 'unit' => 'cup', 'confidence' => 0.4],
            ],
        ]));

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'do chai']);

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.product_uuid', $tea->uuid)
            ->assertJsonPath('data.items.0.name', 'Tea')
            ->assertJsonPath('data.items.0.resolved', true);

        $this->assertEquals(20.0, $response->json('data.items.0.price'));
        $this->assertEquals(2, $response->json('data.items.0.quantity'));
    }

    public function test_voice_parse_resolves_hinglish_menu_items_without_uuid(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $tea = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Tea',
            'price' => 20,
            'unit' => 'plate',
        ]);

        $prantha = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Aloo Prantha',
            'price' => 40,
            'unit' => 'plate',
        ]);

        Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Plain Prantha',
            'price' => 20,
            'unit' => 'plate',
        ]);

        // Gemini matched "parata" but left "chai" unmatched, like the live bug.
        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => '', 'spoken_name' => '2chai', 'quantity' => 2, 'unit' => 'cup', 'confidence' => 0.5],
                ['product_uuid' => $prantha->uuid, 'spoken_name' => 'parata', 'quantity' => 2, 'unit' => 'plate', 'confidence' => 0.8],
            ],
        ]));

        $response = $this->postJson('/api/voice/parse', [
            'transcript' => '2 chai aur 2 parata ka bill banao',
            'language' => 'hi',
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.items.0.product_uuid', $tea->uuid)
            ->assertJsonPath('data.items.0.name', 'Tea')
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.1.product_uuid', $prantha->uuid)
            ->assertJsonPath('data.items.1.name', 'Aloo Prantha')
            ->assertJsonPath('data.items.1.quantity', 2);
    }

    public function test_voice_parse_merges_duplicate_catalog_items(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Tata Salt 1kg',
            'price' => 25,
        ]);

        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => $product->uuid, 'spoken_name' => 'namak', 'quantity' => 2, 'unit' => 'packet', 'confidence' => 0.9],
                ['product_uuid' => $product->uuid, 'spoken_name' => 'namak', 'quantity' => 1, 'unit' => 'packet', 'confidence' => 0.8],
            ],
        ]));

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'do namak aur ek namak']);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.items');

        $this->assertEquals(3, $response->json('data.items.0.quantity'));
    }

    public function test_voice_parse_sanitizes_payment_method_and_discount(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Parle-G Biscuit',
            'price' => 10,
        ]);

        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => $product->uuid, 'spoken_name' => 'biscuit', 'quantity' => 5, 'unit' => 'packet', 'confidence' => 0.9],
            ],
            'payment_method' => 'bitcoin',
            'discount_type' => 'percentage',
            'discount_value' => 250,
        ]));

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'paanch biscuit cash']);

        $response->assertStatus(200)
            ->assertJsonPath('data.payment_method', 'cash')
            ->assertJsonPath('data.discount.type', 'percentage');

        $this->assertEquals(100, $response->json('data.discount.value'));
    }

    public function test_voice_parse_matches_existing_customer(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $customer = Customer::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Ramesh Kumar',
            'phone' => '9876543210',
        ]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Fortune Oil 1L',
            'price' => 150,
        ]);

        $this->fakeGemini($this->draft([
            'items' => [
                ['product_uuid' => $product->uuid, 'spoken_name' => 'tel', 'quantity' => 1, 'unit' => 'litre', 'confidence' => 0.9],
            ],
            'customer_name' => 'Ramesh',
            'payment_method' => 'credit',
        ]));

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'Ramesh ko ek tel udhaar']);

        $response->assertStatus(200)
            ->assertJsonPath('data.customer.uuid', $customer->uuid)
            ->assertJsonPath('data.customer.name', 'Ramesh Kumar')
            ->assertJsonPath('data.customer_name', 'Ramesh Kumar');
    }

    public function test_voice_parse_returns_friendly_error_when_gemini_fails(): void
    {
        [$user] = $this->makeShop();
        Passport::actingAs($user);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'boom'], 500),
        ]);

        $this->postJson('/api/voice/parse', ['transcript' => 'do kilo aata'])
            ->assertStatus(502)
            ->assertJson(['success' => false]);
    }

    public function test_voice_parse_uses_fallback_model_when_primary_is_overloaded(): void
    {
        [$user, $shop] = $this->makeShop();
        Passport::actingAs($user);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'name' => 'Aashirvaad Atta 5kg',
            'price' => 250,
            'unit' => 'kg',
        ]);

        Http::fake([
            '*models/gemini-3.5-flash-lite:generateContent' => Http::response([
                'error' => ['code' => 503, 'message' => 'This model is currently experiencing high demand.'],
            ], 503),
            '*models/gemini-3.1-flash-lite:generateContent' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => json_encode($this->draft([
                            'items' => [
                                ['product_uuid' => $product->uuid, 'spoken_name' => 'aata', 'quantity' => 1, 'unit' => 'kilo', 'confidence' => 0.9],
                            ],
                        ]))]],
                    ],
                ]],
            ]),
        ]);

        $response = $this->postJson('/api/voice/parse', ['transcript' => 'ek kilo aata']);

        $response->assertStatus(200)
            ->assertJsonPath('data.source', 'gemini')
            ->assertJsonPath('data.items.0.product_uuid', $product->uuid);

        Http::assertSentCount(2);
    }

    public function test_voice_parse_requires_gemini_configuration(): void
    {
        [$user] = $this->makeShop();
        Passport::actingAs($user);

        config(['services.gemini.key' => null]);

        Http::fake();

        $this->postJson('/api/voice/parse', ['transcript' => 'do kilo aata'])
            ->assertStatus(503)
            ->assertJson(['success' => false]);

        Http::assertNothingSent();
    }

    public function test_voice_parse_requires_a_shop(): void
    {
        $user = User::factory()->create([
            'phone' => '9999999999',
            'phone_verified_at' => now(),
        ]);
        Passport::actingAs($user);

        $this->postJson('/api/voice/parse', ['transcript' => 'do kilo aata'])
            ->assertStatus(400)
            ->assertJson(['success' => false]);
    }
}
