<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('discount_type', 20)->default('fixed')->after('discount');
            $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->string('discount_type', 20)->default('fixed')->after('discount');
            $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
            $table->decimal('gst_rate', 5, 2)->default(0)->after('tax');
            $table->decimal('cgst', 12, 2)->default(0)->after('gst_rate');
            $table->decimal('sgst', 12, 2)->default(0)->after('cgst');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'gst_rate', 'cgst', 'sgst']);
        });
    }
};
