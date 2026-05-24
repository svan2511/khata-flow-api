<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('name');
            $table->string('slug')->index();
            $table->string('sku', 100)->nullable()->index();
            $table->string('barcode', 100)->nullable()->index();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('mrp', 12, 2)->nullable();
            $table->string('unit', 50)->default('piece');
            $table->decimal('stock_quantity', 12, 2)->default(0);
            $table->decimal('low_stock_threshold', 12, 2)->default(10);
            $table->string('image')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shop_id', 'slug']);
            $table->index(['shop_id', 'sku']);
            $table->index(['shop_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
