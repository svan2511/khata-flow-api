<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::factory()->create([
            'name' => 'Demo Owner',
            'email' => 'demo@dukaansahayak.app',
            'phone' => '9999999999',
            'phone_verified_at' => now(),
        ]);

        $shop = Shop::factory()->create([
            'user_id' => $demoUser->id,
            'shop_name' => 'KhataFlow General Store',
            'owner_name' => 'Demo Owner',
            'phone' => '9999999999',
            'email' => 'shop@khataflow.app',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'gstin' => '27ABCDE1234F1Z5',
            'is_active' => true,
        ]);

        $categories = ProductCategory::factory()->count(5)->create([
            'shop_id' => $shop->id,
        ]);

        $products = Product::factory()->count(20)->create([
            'shop_id' => $shop->id,
            'product_category_id' => fn () => $categories->random()->id,
        ]);

        Product::factory()->count(5)->lowStock()->create([
            'shop_id' => $shop->id,
            'product_category_id' => fn () => $categories->random()->id,
        ]);

        $customers = Customer::factory()->count(10)->create([
            'shop_id' => $shop->id,
        ]);

        $today = Carbon::today();

        for ($i = 1; $i <= 8; $i++) {
            $customer = $customers->random();
            $total = fake()->randomFloat(2, 50, 2000);
            $paid = $total > 500 ? $total * 0.5 : $total;
            $paymentStatus = $total > 500 ? 'partial' : 'paid';

            $bill = Bill::create([
                'uuid' => (string) Str::uuid(),
                'bill_number' => 'BILL-'.$today->format('Ymd').'-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'shop_id' => $shop->id,
                'user_id' => $demoUser->id,
                'customer_id' => $customer->id,
                'subtotal' => $total,
                'discount' => 0,
                'tax' => $total * 0.05,
                'total' => $total + ($total * 0.05),
                'paid_amount' => $paid,
                'due_amount' => $total - $paid,
                'payment_status' => $paymentStatus,
                'payment_method' => 'cash',
                'created_at' => $today->copy()->addHours(rand(8, 20)),
                'updated_at' => $today,
            ]);

            $billItemsCount = rand(1, 5);
            $billSubtotal = 0;
            $selectedProducts = $products->random($billItemsCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $itemTotal = $product->price * $quantity;
                $billSubtotal += $itemTotal;

                BillItem::create([
                    'uuid' => (string) Str::uuid(),
                    'bill_id' => $bill->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'discount' => 0,
                    'tax' => 0,
                    'subtotal' => $itemTotal,
                    'total' => $itemTotal,
                ]);
            }

            $bill->update([
                'subtotal' => $billSubtotal,
                'total' => $billSubtotal,
            ]);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Phone: 9999999999');
        $this->command->info('OTP: 123456 (use for testing)');
    }
}
