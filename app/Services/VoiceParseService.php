<?php

namespace App\Services;

use App\DTOs\VoiceParseDTO;
use App\DTOs\VoiceParseItemDTO;
use App\DTOs\VoiceParseResultDTO;
use App\Exceptions\VoiceParseException;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VoiceParseService
{
    private const MAX_CANDIDATES = 25;

    private const MAX_ITEMS = 30;

    private const DEFAULT_UNIT = 'piece';

    private const PAYMENT_METHODS = ['cash', 'upi', 'card', 'credit', 'mix'];

    private const STOPWORDS = [
        'aur', 'and', 'plus', 'ka', 'ki', 'ke', 'ko', 'se', 'me', 'mein', 'par', 'pe',
        'de', 'do', 'dena', 'dijiye', 'likh', 'likho', 'likhdo', 'add', 'karo', 'kar',
        'kardo', 'ek', 'teen', 'char', 'chaar', 'paanch', 'panch', 'chhe', 'chhah', 'che',
        'or', 'ya', 'yaa', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'saat', 'aath', 'nau', 'das', 'bees', 'pachas', 'sau', 'dedh', 'dhai', 'aadha', 'adha',
        'kilo', 'kg', 'gram', 'gm', 'packet', 'pack', 'pcs', 'pc', 'piece', 'pieces',
        'litre', 'liter', 'ltr', 'ml', 'dozen', 'darjan', 'box', 'plate', 'bori', 'quintal',
        'rupaye', 'rupees', 'rs', 'discount', 'chhoot', 'chhut', 'percent', 'pratishat',
        'udhaar', 'udhar', 'credit', 'cash', 'upi', 'card', 'baaki', 'baki', 'naam',
        'bhai', 'ji', 'sahab', 'sahib', 'please', 'plz', 'ok', 'bhej', 'bhejo', 'bill',
        'banao', 'bana', 'banaao', 'naya', 'new', 'aaj', 'today',
        'का', 'की', 'के', 'को', 'से', 'में', 'पर', 'दो', 'देना', 'लिखो', 'लिख', 'और', 'या',
        'एक', 'तीन', 'चार', 'पांच', 'पाँच', 'छह', 'सात', 'आठ', 'नौ', 'दस', 'बीस',
        'पचास', 'सौ', 'डेढ़', 'ढाई', 'आधा', 'किलो', 'ग्राम', 'पैकेट', 'पीस', 'लीटर',
        'दर्जन', 'बॉक्स', 'रुपये', 'छूट', 'प्रतिशत', 'उधार', 'कैश', 'नकद', 'नाम',
        'भाई', 'जी', 'बिल', 'बनाओ',
    ];

    /**
     * Hindi/Hinglish words mapped to catalog-friendly search terms.
     *
     * @var array<string, string>
     */
    private const PRODUCT_ALIASES = [
        'aata' => 'atta', 'atta' => 'atta', 'आटा' => 'atta',
        'chawal' => 'rice', 'rice' => 'rice', 'चावल' => 'rice',
        'cheeni' => 'sugar', 'chini' => 'sugar', 'sugar' => 'sugar', 'चीनी' => 'sugar',
        'doodh' => 'milk', 'milk' => 'milk', 'दूध' => 'milk',
        'tel' => 'oil', 'oil' => 'oil', 'तेल' => 'oil',
        'sabun' => 'soap', 'soap' => 'soap', 'साबुन' => 'soap',
        'namak' => 'salt', 'salt' => 'salt', 'नमक' => 'salt',
        'maggi' => 'maggi', 'maggie' => 'maggi', 'magi' => 'maggi', 'मैगी' => 'maggi',
        'dal' => 'dal', 'daal' => 'dal', 'दाल' => 'dal',
        'chai' => 'tea', 'chay' => 'tea', 'chaay' => 'tea', 'chae' => 'tea',
        'tea' => 'tea', 'चाय' => 'tea', 'चाए' => 'tea',
        'paratha' => 'prantha', 'parata' => 'prantha', 'parantha' => 'prantha',
        'parontha' => 'prantha', 'prantha' => 'prantha', 'पराठा' => 'prantha', 'परांठा' => 'prantha',
        'aalu' => 'aloo', 'aloo' => 'aloo', 'alu' => 'aloo', 'आलू' => 'aloo',
        'gobi' => 'gobhi', 'gobhi' => 'gobhi', 'गोभी' => 'gobhi',
        'matar' => 'mutter', 'mutter' => 'mutter', 'मटर' => 'mutter',
        'chhole' => 'chole', 'chole' => 'chole', 'छोले' => 'chole',
        'rajma' => 'rajma', 'राजमा' => 'rajma',
        'kadhi' => 'kadhi', 'kadi' => 'kadhi', 'कढ़ी' => 'kadhi',
        'thali' => 'thali', 'थाली' => 'thali',
        'samosa' => 'samosa', 'samosha' => 'samosa', 'समोसा' => 'samosa',
        'bhature' => 'bhature', 'bhatura' => 'bhature', 'भटूरे' => 'bhature',
        'puri' => 'puri', 'poori' => 'puri', 'पूरी' => 'puri',
        'roti' => 'roti', 'रोटी' => 'roti',
        'naan' => 'naan', 'नान' => 'naan',
        'momo' => 'momo', 'momos' => 'momo', 'मोमो' => 'momo', 'मोमोज़' => 'momo',
        'chowmein' => 'chowmein', 'chowmin' => 'chowmein', 'chow' => 'chowmein', 'चाउमीन' => 'chowmein',
        'noodels' => 'noodles', 'noodles' => 'noodles', 'नूडल्स' => 'noodles',
        'manchurian' => 'manchurian', 'मंचूरियन' => 'manchurian',
        'burger' => 'burger', 'बर्गर' => 'burger',
        'sandwich' => 'sandwich', 'सैंडविच' => 'sandwich',
        'pasta' => 'pasta', 'पास्ता' => 'pasta',
        'soup' => 'soup', 'सूप' => 'soup',
        'fries' => 'french', 'frenchfries' => 'french', 'फ्रेंच फ्राइज़' => 'french',
        'lassi' => 'lassi', 'लस्सी' => 'lassi',
        'raita' => 'raita', 'रायता' => 'raita',
        'shake' => 'shake', 'शेक' => 'shake',
        'biscuit' => 'biscuit', 'बिस्किट' => 'biscuit',
        'masala' => 'masala', 'मसाला' => 'masala',
        'ghee' => 'ghee', 'घी' => 'ghee',
        'paneer' => 'paneer', 'पनीर' => 'paneer',
        'anda' => 'egg', 'egg' => 'egg', 'अंडा' => 'egg',
        'bread' => 'bread', 'ब्रेड' => 'bread',
        'shampoo' => 'shampoo', 'शैम्पू' => 'shampoo',
        'surf' => 'surf', 'सर्फ' => 'surf',
        'agarbatti' => 'agarbatti', 'अगरबत्ती' => 'agarbatti',
        'namkeen' => 'namkeen', 'नमकीन' => 'namkeen',
        'coffee' => 'coffee', 'कॉफी' => 'coffee',
        'poha' => 'poha', 'पोहा' => 'poha',
        'suji' => 'suji', 'sooji' => 'suji', 'सूजी' => 'suji',
        'maida' => 'maida', 'मैदा' => 'maida',
        'besan' => 'besan', 'बेसन' => 'besan',
        'haldi' => 'haldi', 'हल्दी' => 'haldi',
        'mirchi' => 'mirchi', 'मिर्च' => 'mirchi',
        'jeera' => 'jeera', 'जीरा' => 'jeera',
        'dhania' => 'dhania', 'धनिया' => 'dhania',
        'toothpaste' => 'toothpaste', 'टूथपेस्ट' => 'toothpaste',
        'brush' => 'brush', 'ब्रश' => 'brush',
        'water' => 'water', 'पानी' => 'water',
        'papad' => 'papad', 'पापड़' => 'papad',
        'achar' => 'achar', 'अचार' => 'achar',
        'curd' => 'curd', 'dahi' => 'curd', 'दही' => 'curd',
        'butter' => 'butter', 'मक्खन' => 'butter',
        'cheese' => 'cheese', 'चीज़' => 'cheese',
        'ketchup' => 'ketchup', 'केचप' => 'ketchup',
        'noodles' => 'noodles', 'नूडल्स' => 'noodles',
        'chocolate' => 'chocolate', 'चॉकलेट' => 'chocolate',
        'chips' => 'chips', 'चिप्स' => 'chips',
        'lotion' => 'lotion', 'लोशन' => 'lotion',
        'powder' => 'powder', 'पाउडर' => 'powder',
        'matchbox' => 'matchbox', 'माचिस' => 'matchbox',
        'candle' => 'candle', 'मोमबत्ती' => 'candle',
        'battery' => 'battery', 'बैटरी' => 'battery',
        'bulb' => 'bulb', 'बल्ब' => 'bulb',
    ];

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ShopRepositoryInterface $shopRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    public function parse(User $user, VoiceParseDTO $dto): VoiceParseResultDTO
    {
        $startedAt = microtime(true);

        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new VoiceParseException('Shop not found. Please setup your shop first.');
        }

        $apiKey = (string) config('services.gemini.key');

        if ($apiKey === '') {
            throw new VoiceParseException('Voice billing is not configured on the server.', 503);
        }

        $candidates = $this->collectCandidates($shop->id, $dto->transcript);

        $response = $this->callGemini($apiKey, $dto, $candidates, $shop);

        $result = $this->buildResult($this->decodeResponse($response), $candidates, $shop->id);

        Log::info('Voice bill parsed', [
            'shop_id' => $shop->id,
            'items' => count($result->items),
            'payment_method' => $result->paymentMethod,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return $result;
    }

    /**
     * Collect the most relevant catalog products to ground the model.
     *
     * @return Collection<int, Product>
     */
    private function collectCandidates(int $shopId, string $transcript): Collection
    {
        $candidates = collect();

        foreach ($this->extractSearchTerms($transcript) as $term) {
            $candidates = $candidates->merge(
                $this->productRepository->search($term, $shopId, 6)
            );
        }

        if ($candidates->count() < 12) {
            $fallback = $this->productRepository->paginate($shopId, ['per_page' => 40])->items();
            $candidates = $candidates->merge($fallback);
        }

        return $candidates
            ->unique('uuid')
            ->take(self::MAX_CANDIDATES)
            ->values();
    }

    /**
     * @return string[]
     */
    private function extractSearchTerms(string $transcript): array
    {
        $normalized = Str::lower(trim($transcript));
        $normalized = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $normalized) ?? '';
        $normalized = preg_replace('/(?<=\d)(?=\p{L})|(?<=\p{L})(?=\d)/u', ' ', $normalized) ?? '';
        $tokens = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $terms = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 2 || in_array($token, self::STOPWORDS, true)) {
                continue;
            }

            if (preg_match('/^[\d.,]+$/u', $token) === 1) {
                continue;
            }

            if (isset(self::PRODUCT_ALIASES[$token])) {
                $terms[] = self::PRODUCT_ALIASES[$token];
            }

            $terms[] = $token;
        }

        $terms[] = $normalized;

        return array_values(array_unique(array_slice($terms, 0, 10)));
    }

    /**
     * @param  Collection<int, Product>  $candidates
     */
    private function callGemini(string $apiKey, VoiceParseDTO $dto, Collection $candidates, Shop $shop): Response
    {
        $model = (string) config('services.gemini.model', 'gemini-3.5-flash-lite');
        $fallbackModels = collect(explode(',', (string) config('services.gemini.fallback_models', '')))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->all();
        $models = array_values(array_unique(array_filter(array_merge([$model], $fallbackModels))));
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $timeout = (int) config('services.gemini.timeout', 15);

        $catalog = $candidates->map(fn (Product $product) => [
            'product_uuid' => $product->uuid,
            'name' => $product->name,
            'price' => (float) $product->price,
            'unit' => $product->unit,
            'stock' => (float) $product->stock_quantity,
        ])->values()->all();

        $userPrompt = sprintf(
            "Shop: %s\nLanguage: %s\nSpoken instruction:\n\"%s\"\n\nCatalog (only these items can be matched):\n%s",
            $shop->shop_name,
            $dto->language,
            $dto->transcript,
            json_encode($catalog, JSON_UNESCAPED_UNICODE)
        );

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $this->systemPrompt()]],
            ],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
                'responseJsonSchema' => $this->responseSchema(),
            ],
        ];

        $lastResponse = null;

        foreach ($models as $candidateModel) {
            $response = Http::timeout($timeout)
                ->connectTimeout(5)
                ->acceptJson()
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->post("{$baseUrl}/models/{$candidateModel}:generateContent", $payload);

            if (! $response->failed()) {
                if ($candidateModel !== $model) {
                    Log::info('Gemini voice parse used fallback model', ['model' => $candidateModel]);
                }

                return $response;
            }

            $lastResponse = $response;

            Log::warning('Gemini voice parse failed', [
                'model' => $candidateModel,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);

            if (! in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                break;
            }
        }

        $status = $lastResponse?->status() ?? 502;

        $message = $status === 429
            ? 'Voice service is busy. Please try again in a minute.'
            : 'Voice service is unavailable. Please try again.';

        throw new VoiceParseException($message, $status === 429 ? 429 : 502);
    }

    private function decodeResponse(Response $response): array
    {
        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            Log::warning('Gemini voice parse returned an empty response', [
                'body' => Str::limit($response->body(), 500),
            ]);

            throw new VoiceParseException('Voice service returned an empty response. Please try again.', 502);
        }

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            Log::warning('Gemini voice parse returned invalid JSON', [
                'text' => Str::limit($text, 500),
            ]);

            throw new VoiceParseException('Voice service returned an invalid response. Please try again.', 502);
        }

        return $decoded;
    }

    /**
     * @param  Collection<int, Product>  $candidates
     */
    private function buildResult(array $payload, Collection $candidates, int $shopId): VoiceParseResultDTO
    {
        $byUuid = $candidates->keyBy('uuid');
        $merged = [];

        foreach (array_slice((array) ($payload['items'] ?? []), 0, self::MAX_ITEMS) as $rawItem) {
            if (! is_array($rawItem)) {
                continue;
            }

            $spokenName = trim((string) ($rawItem['spoken_name'] ?? ''));
            $uuid = trim((string) ($rawItem['product_uuid'] ?? ''));

            if ($spokenName === '' && $uuid === '') {
                continue;
            }

            $quantity = round((float) ($rawItem['quantity'] ?? 0), 2);
            $quantity = max(0.01, min($quantity, 999999));
            $confidence = max(0.0, min((float) ($rawItem['confidence'] ?? 0), 1.0));

            /** @var Product|null $product */
            $product = $uuid !== '' ? $byUuid->get($uuid) : null;

            if (! $product && $spokenName !== '') {
                $product = $this->matchCatalogProduct($spokenName, $shopId);
            }

            if ($product) {
                $key = $product->uuid;
                $item = new VoiceParseItemDTO(
                    productUuid: $product->uuid,
                    name: $product->name,
                    price: (float) $product->price,
                    quantity: $quantity,
                    unit: $product->unit ?: self::DEFAULT_UNIT,
                    gstRate: 0.0,
                    confidence: $confidence,
                    resolved: true,
                    spokenName: $spokenName !== '' ? $spokenName : $product->name,
                );
            } else {
                $key = 'unresolved:'.Str::lower($spokenName);
                $item = new VoiceParseItemDTO(
                    productUuid: null,
                    name: $spokenName !== '' ? $spokenName : 'Custom item',
                    price: 0.0,
                    quantity: $quantity,
                    unit: trim((string) ($rawItem['unit'] ?? '')) ?: self::DEFAULT_UNIT,
                    gstRate: 0.0,
                    confidence: $confidence,
                    resolved: false,
                    spokenName: $spokenName,
                );
            }

            if (isset($merged[$key])) {
                $existing = $merged[$key];
                $merged[$key] = new VoiceParseItemDTO(
                    productUuid: $existing->productUuid,
                    name: $existing->name,
                    price: $existing->price,
                    quantity: max(0.01, min(round($existing->quantity + $item->quantity, 2), 999999)),
                    unit: $existing->unit,
                    gstRate: $existing->gstRate,
                    confidence: max($existing->confidence, $item->confidence),
                    resolved: $existing->resolved,
                    spokenName: $existing->spokenName,
                );

                continue;
            }

            $merged[$key] = $item;
        }

        $items = array_values($merged);

        if ($items === []) {
            throw new VoiceParseException('Kuch samajh nahi aaya. Dobara bolke try karein.', 422);
        }

        $paymentMethod = Str::lower(trim((string) ($payload['payment_method'] ?? 'cash')));

        if (! in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
            $paymentMethod = 'cash';
        }

        $discountType = Str::lower(trim((string) ($payload['discount_type'] ?? 'none')));
        $discountValue = round((float) ($payload['discount_value'] ?? 0), 2);
        $discount = null;

        if ($discountValue > 0) {
            if ($discountType === 'percentage') {
                $discount = ['type' => 'percentage', 'value' => min($discountValue, 100.0)];
            } elseif ($discountType === 'fixed') {
                $discount = ['type' => 'fixed', 'value' => min($discountValue, 999999.0)];
            }
        }

        $customerName = trim((string) ($payload['customer_name'] ?? ''));
        $customer = null;

        if ($customerName !== '') {
            $match = $this->customerRepository->search($customerName, $shopId, 5)->first();

            if ($match) {
                $customer = [
                    'uuid' => $match->uuid,
                    'name' => $match->name,
                    'phone' => $match->phone,
                ];
                $customerName = $match->name;
            }
        }

        return new VoiceParseResultDTO(
            items: $items,
            customer: $customer,
            customerName: $customerName,
            paymentMethod: $paymentMethod,
            discount: $discount,
            notes: Str::limit(trim((string) ($payload['notes'] ?? '')), 500, ''),
            assistantReply: Str::limit(trim((string) ($payload['assistant_reply'] ?? '')), 300, ''),
            source: 'gemini',
        );
    }

    /**
     * Deterministically resolve a spoken name that the model left unmatched.
     * Example: "chai" -> catalog product "Tea" via PRODUCT_ALIASES.
     */
    private function matchCatalogProduct(string $spokenName, int $shopId): ?Product
    {
        $normalized = $this->normalizeProductText($spokenName);

        if ($normalized === '') {
            return null;
        }

        $tokens = array_values(array_filter(
            explode(' ', $normalized),
            fn (string $token) => mb_strlen($token) >= 2
                && ! in_array($token, self::STOPWORDS, true)
                && preg_match('/^[\d.,]+$/u', $token) !== 1
        ));

        if ($tokens === []) {
            return null;
        }

        $terms = [];

        foreach ($tokens as $token) {
            $terms[] = $token;

            if (isset(self::PRODUCT_ALIASES[$token])) {
                $terms[] = self::PRODUCT_ALIASES[$token];
            }
        }

        $terms = array_values(array_unique($terms));
        $scored = [];

        foreach ($terms as $term) {
            foreach ($this->productRepository->search($term, $shopId, 10) as $product) {
                $score = $this->scoreProductMatch($product->name, $normalized, $tokens, $terms);

                if ($score <= 0) {
                    continue;
                }

                $uuid = $product->uuid;

                if (! isset($scored[$uuid]) || $scored[$uuid]['score'] < $score) {
                    $scored[$uuid] = ['product' => $product, 'score' => $score];
                }
            }
        }

        if ($scored === []) {
            return null;
        }

        $ranked = array_values($scored);
        usort($ranked, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        $best = $ranked[0];

        if ($best['score'] < 50) {
            return null;
        }

        if (isset($ranked[1]) && ($best['score'] - $ranked[1]['score']) < 10) {
            return null;
        }

        Log::info('Voice item resolved from catalog aliases', [
            'spoken_name' => $spokenName,
            'product' => $best['product']->name,
            'score' => $best['score'],
        ]);

        return $best['product'];
    }

    /**
     * @param  string[]  $tokens
     * @param  string[]  $terms
     */
    private function scoreProductMatch(string $productName, string $spokenName, array $tokens, array $terms): float
    {
        $productName = $this->normalizeProductText($productName);
        $productTokens = explode(' ', $productName);
        $score = 0.0;

        foreach ($terms as $term) {
            if ($productName === $term) {
                $score += 100;
            } elseif (in_array($term, $productTokens, true)) {
                $score += 60;
            } elseif (str_contains($productName, $term)) {
                $score += 40;
            }
        }

        if ($productName === $spokenName) {
            $score += 100;
        }

        foreach ($tokens as $token) {
            if (in_array($token, $productTokens, true)) {
                $score += 20;
            } elseif (str_contains($productName, $token)) {
                $score += 10;
            }
        }

        return $score - (mb_strlen($productName) * 0.1);
    }

    private function normalizeProductText(string $value): string
    {
        $normalized = Str::lower(trim($value));
        $normalized = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $normalized) ?? '';
        $normalized = preg_replace('/(?<=\d)(?=\p{L})|(?<=\p{L})(?=\d)/u', ' ', $normalized) ?? '';
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? '';

        return trim($normalized);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are a billing assistant for an Indian kirana (grocery) store. A shopkeeper speaks a bill in Hindi, Hinglish or English. Convert it into a structured bill draft using ONLY the catalog items provided by the user.

Rules:
1. Match each spoken product to the closest catalog item, even when the catalog name is in a different language, script or spelling. The shopkeeper may speak Hindi, Hinglish or English, so treat translations, transliterations and common synonyms as the same item. Examples: "chai"/"चाय" -> Tea, "paratha"/"parata"/"prantha"/"पराठा" -> a Prantha item, "aata"/"आटा" -> Atta, "doodh"/"दूध" -> Milk, "aloo"/"aalu"/"आलू" -> Aloo, "maggie"/"maggi"/"मैगी" -> Maggie, "noodles"/"नूडल्स" -> Noodles. Prefer the catalog item that shares the core word over a vaguely similar item. Never match unrelated items. Only leave product_uuid empty when no catalog item is even related.
2. Copy product_uuid exactly from the catalog. If nothing in the catalog reasonably matches, set product_uuid to an empty string and put the shopkeeper's words in spoken_name.
3. Never invent prices. The server takes prices and units from the catalog; you only decide quantity, unit hint and spoken_name.
4. Convert spoken quantities to numbers: ek=1, do=2, teen=3, char/chaar=4, paanch=5, chhe=6, saat=7, aath=8, nau=9, das=10, dedh=1.5, dhai=2.5, aadha=0.5, sau=100. Understand units: kilo/kg, gram/gm, packet, piece/pcs, litre/ltr, ml, dozen/darjan. If no unit is spoken, use the catalog unit.
5. Detect the payment method: cash, upi, card, credit (udhaar/baaki) or mix. Default cash.
6. Detect a bill-level discount: "X percent chhoot" -> percentage, "X rupaye kam" -> fixed. Otherwise discount_type = none and discount_value = 0.
7. Detect a customer name when spoken ("Ramesh ke naam", "Ramesh ko udhaar"). Otherwise customer_name = "".
8. assistant_reply: one short friendly sentence in the same language the shopkeeper used, summarising what you understood.
9. If the instruction contains no clear product, return an empty items array.

Return only JSON that matches the provided schema.
PROMPT;
    }

    private function responseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'product_uuid' => [
                                'type' => 'string',
                                'description' => 'Exact catalog product_uuid, or an empty string when nothing matches.',
                            ],
                            'spoken_name' => ['type' => 'string'],
                            'quantity' => ['type' => 'number'],
                            'unit' => ['type' => 'string'],
                            'confidence' => ['type' => 'number'],
                        ],
                        'required' => ['product_uuid', 'spoken_name', 'quantity', 'unit', 'confidence'],
                    ],
                ],
                'customer_name' => ['type' => 'string'],
                'payment_method' => [
                    'type' => 'string',
                    'enum' => self::PAYMENT_METHODS,
                ],
                'discount_type' => [
                    'type' => 'string',
                    'enum' => ['none', 'percentage', 'fixed'],
                ],
                'discount_value' => ['type' => 'number'],
                'notes' => ['type' => 'string'],
                'assistant_reply' => ['type' => 'string'],
            ],
            'required' => [
                'items',
                'customer_name',
                'payment_method',
                'discount_type',
                'discount_value',
                'notes',
                'assistant_reply',
            ],
        ];
    }
}
