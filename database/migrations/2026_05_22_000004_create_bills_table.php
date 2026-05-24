<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete()->index();
            $table->string('name');
            $table->string('phone', 20)->nullable()->index();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('total_credit', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shop_id', 'phone']);
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('bill_number', 50)->index();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('payment_status', 50)->default('pending')->index();
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shop_id', 'bill_number']);
            $table->index(['shop_id', 'payment_status']);
            $table->index(['shop_id', 'created_at']);
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('product_name');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('bill_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50);
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shop_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('customers');
    }
};
