<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('phone', 20)->index();
            $table->string('otp', 6);
            $table->string('purpose', 50)->index();
            $table->boolean('is_used')->default(false)->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['phone', 'purpose', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
