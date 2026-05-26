<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->index(['bill_id', 'product_id'], 'bill_items_bill_product_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['shop_id', 'total_credit'], 'customers_shop_credit_index');
        });
    }

    public function down(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropIndex('bill_items_bill_product_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_shop_credit_index');
        });
    }
};
