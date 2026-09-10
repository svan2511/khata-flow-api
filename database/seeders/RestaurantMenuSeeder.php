<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantMenuSeeder extends Seeder
{
    public function run(): void
    {
        $shopId = 1;

        // Categories (shop 1 ke liye create/find)
        $categoryNames = [
            'Break Fast',
            'Snacks',
            'Indian Thali',
            'Indian Maincourse',
            'Paneer Dishes',
            'Vegetable',
            'Daal',
            'Chinese',
            'Raita',
            'Maggie',
            'Beverages',
        ];

        $categoryIds = [];
        foreach ($categoryNames as $catName) {
            $cat = ProductCategory::firstOrCreate(
                ['shop_id' => $shopId, 'name' => $catName],
                [
                    'uuid' => (string) Str::uuid(),
                    'slug' => Str::slug($catName.'-shop-'.$shopId.'-'.Str::random(4)),
                    'description' => $catName.' menu items',
                    'is_active' => true,
                ]
            );
            $categoryIds[$catName] = $cat->id;
        }

        // [name, price, category]
        $items = [
            // ---- BREAK FAST ----
            ['Aloo Prantha', 40, 'Break Fast'],
            ['Aloo Piyaj Prantha', 50, 'Break Fast'],
            ['Gobhi Prantha', 60, 'Break Fast'],
            ['Mix Prantha', 70, 'Break Fast'],
            ['Paneer Prantha', 80, 'Break Fast'],
            ['Plain Prantha', 20, 'Break Fast'],
            ['Piyaaz Prantha', 50, 'Break Fast'],
            ['Plain Roti', 10, 'Break Fast'],
            ['Butter Roti', 15, 'Break Fast'],
            ['Puri Bhaji (4 Piece)', 60, 'Break Fast'],
            ['Chole Bhature', 80, 'Break Fast'],
            ['Veg Sandwich', 50, 'Break Fast'],
            ['Cheese Sandwich', 70, 'Break Fast'],
            ['Paneer Sandwich', 80, 'Break Fast'],
            ['Butter Toast', 50, 'Break Fast'],
            ['Plain Burger', 50, 'Break Fast'],
            ['Cheese Burger', 60, 'Break Fast'],
            ['Paneer Burger', 70, 'Break Fast'],

            // ---- SNACKS ----
            ['Samosa', 20, 'Snacks'],
            ['Bread Pakoda', 20, 'Snacks'],
            ['Paneer Pakoda (10 Pcs.)', 100, 'Snacks'],
            ['Mix Pakoda (250 Gm.)', 80, 'Snacks'],
            ['Chole Samosa', 40, 'Snacks'],
            ['Dahi Samosa', 50, 'Snacks'],

            // ---- INDIAN THALI ----
            ['Veg Thali (Sabji+Dal+Roti+Salad+Rice)', 80, 'Indian Thali'],
            ['Special Thali (Paneer+Dal+4 Roti+Raita+Rice+Salad)', 120, 'Indian Thali'],

            // ---- INDIAN MAINCOURSE (Half / Full split) ----
            ['Rajma Chawal Half', 40, 'Indian Maincourse'],
            ['Rajma Chawal Full', 70, 'Indian Maincourse'],
            ['Kadhi Chawal Half', 40, 'Indian Maincourse'],
            ['Kadhi Chawal Full', 70, 'Indian Maincourse'],
            ['Chole Chawal Half', 40, 'Indian Maincourse'],
            ['Chole Chawal Full', 70, 'Indian Maincourse'],
            ['Daal Chawal Half', 40, 'Indian Maincourse'],
            ['Daal Chawal Full', 70, 'Indian Maincourse'],

            // ---- PANEER DISHES (Half / Full split) ----
            ['Mutter Paneer Half', 150, 'Paneer Dishes'],
            ['Mutter Paneer Full', 260, 'Paneer Dishes'],
            ['Kadhai Paneer Half', 180, 'Paneer Dishes'],
            ['Kadhai Paneer Full', 290, 'Paneer Dishes'],
            ['Paneer Butter Masala Half', 180, 'Paneer Dishes'],
            ['Paneer Butter Masala Full', 300, 'Paneer Dishes'],
            ['Paneer Bhurji Half', 180, 'Paneer Dishes'],
            ['Paneer Bhurji Full', 300, 'Paneer Dishes'],
            ['Shahi Paneer Half', 180, 'Paneer Dishes'],
            ['Shahi Paneer Full', 290, 'Paneer Dishes'],

            // ---- VEGETABLE (Half / Full split, Sev Bhaji only Full) ----
            ['Mix Veg Half', 90, 'Vegetable'],
            ['Mix Veg Full', 160, 'Vegetable'],
            ['Aaloo Matter Half', 80, 'Vegetable'],
            ['Aaloo Matter Full', 150, 'Vegetable'],
            ['Aaloo Shimla Half', 80, 'Vegetable'],
            ['Aaloo Shimla Full', 150, 'Vegetable'],
            ['Aaloo Gobhi Half', 80, 'Vegetable'],
            ['Aaloo Gobhi Full', 150, 'Vegetable'],
            ['Gobhi Masala Half', 80, 'Vegetable'],
            ['Gobhi Masala Full', 150, 'Vegetable'],
            ['Aaloo Jeera Half', 60, 'Vegetable'],
            ['Aaloo Jeera Full', 100, 'Vegetable'],
            ['Sev Bhaji Full', 150, 'Vegetable'],

            // ---- DAAL (Half / Full split) ----
            ['Daal Fry Half', 70, 'Daal'],
            ['Daal Fry Full', 130, 'Daal'],
            ['Daal Tadka Half', 70, 'Daal'],
            ['Daal Tadka Full', 130, 'Daal'],
            ['Dal Makhani Half', 100, 'Daal'],
            ['Dal Makhani Full', 180, 'Daal'],
            ['Rajma Half', 80, 'Daal'],
            ['Rajma Full', 140, 'Daal'],
            ['Chole Half', 80, 'Daal'],
            ['Chole Full', 140, 'Daal'],
            ['Chana Masala Half', 80, 'Daal'],
            ['Chana Masala Full', 140, 'Daal'],
            ['Kadhi Pakoda Half', 60, 'Daal'],
            ['Kadhi Pakoda Full', 100, 'Daal'],

            // ---- CHINESE ----
            ['Veg Noodles', 60, 'Chinese'],
            ['Hakka Noodles', 80, 'Chinese'],
            ['Sahjawan Noodles', 90, 'Chinese'],
            ['Paneer Noodles', 120, 'Chinese'],
            ['Garlic Noodles', 80, 'Chinese'],
            ['Veg Momos (8 Pcs)', 70, 'Chinese'],
            ['Fried Momos (8 Pcs)', 100, 'Chinese'],
            ['Kurkure Momos (8 Pcs)', 120, 'Chinese'],
            ['Tandoori Momos (8 Pcs)', 140, 'Chinese'],
            ['Manchurian (Dry)', 140, 'Chinese'],
            ['Manchurian (Gravy)', 120, 'Chinese'],
            ['Chili Potato', 120, 'Chinese'],
            ['Honey Chili Potato', 180, 'Chinese'],
            ['French Fry', 140, 'Chinese'],
            ['Peri Peri', 120, 'Chinese'],
            ['White Sauce Pasta', 180, 'Chinese'],
            ['Red Sauce Pasta', 160, 'Chinese'],
            ['Mix Sauce Pasta', 150, 'Chinese'],
            ['Chili Paneer (Gravy)', 160, 'Chinese'],
            ['Chili Paneer (Dry)', 180, 'Chinese'],
            ['Veg Fried Rice', 120, 'Chinese'],
            ['Paneer Fried Rice', 140, 'Chinese'],
            ['Veg Sezchwan Rice', 120, 'Chinese'],
            ['Jeera Rice', 120, 'Chinese'],
            ['Steam Rice', 100, 'Chinese'],
            ['Spring Roll', 70, 'Chinese'],

            // ---- RAITA ----
            ['Vegetable Raita Half', 70, 'Raita'],
            ['Vegetable Raita Full', 120, 'Raita'],
            ['Bundi Raita Half', 60, 'Raita'],
            ['Bundi Raita Full', 100, 'Raita'],
            ['Plain Dahi (Curd) Small', 20, 'Raita'],
            ['Plain Dahi (Curd) Medium', 40, 'Raita'],
            ['Plain Dahi (Curd) Large', 80, 'Raita'],

            // ---- MAGGIE ----
            ['Plain Maggie', 50, 'Maggie'],
            ['Veg Maggie', 60, 'Maggie'],
            ['Paneer Maggie', 80, 'Maggie'],
            ['Cheese Maggie', 70, 'Maggie'],

            // ---- BEVERAGES ----
            ['Tea', 20, 'Beverages'],
            ['Masala Tea', 30, 'Beverages'],
            ['Lemon Tea', 40, 'Beverages'],
            ['Black Tea', 40, 'Beverages'],
            ['Green Tea', 40, 'Beverages'],
            ['Ice Tea', 70, 'Beverages'],
            ['Hot Coffee', 60, 'Beverages'],
            ['Cold Coffee', 130, 'Beverages'],
            ['Black Coffee', 40, 'Beverages'],
            ['Banana Shake', 80, 'Beverages'],
            ['Mango Shake', 80, 'Beverages'],
            ['Kitkat Shake', 100, 'Beverages'],
            ['Oreo Shake', 100, 'Beverages'],
            ['Vanilla Shake', 100, 'Beverages'],
            ['Badam Shake', 120, 'Beverages'],
            ['Fresh Lemon Soda', 70, 'Beverages'],
            ['Mint Mojito', 100, 'Beverages'],
            ['Blue Lagoon', 100, 'Beverages'],
            ['Shikanji', 50, 'Beverages'],
            ['Lemon Water', 50, 'Beverages'],
            ['Masala Chhach', 40, 'Beverages'],
            ['Sweet Lassi', 70, 'Beverages'],
            // Mineral Water + Cold Drink MRP pe hai isliye skip kiya
        ];

        $count = 0;
        foreach ($items as [$name, $price, $catName]) {
            Product::updateOrCreate(
                ['shop_id' => $shopId, 'name' => $name],
                [
                    // NOTE: uuid yaha set mat karo — Product model booted() me
                    // create pe auto-generate karta hai. Har re-run pe uuid
                    // badalne se app me saved references toot jayenge.
                    'product_category_id' => $categoryIds[$catName],
                    'price' => $price,
                    'mrp' => $price,
                    'unit' => 'plate',
                    'stock_quantity' => 1000,
                    'low_stock_threshold' => 10,
                    'description' => $name,
                    'is_active' => true,
                ]
            );
            $count++;
        }

        $this->command->info("Restaurant menu seeded: {$count} products for shop {$shopId}");
    }
}
