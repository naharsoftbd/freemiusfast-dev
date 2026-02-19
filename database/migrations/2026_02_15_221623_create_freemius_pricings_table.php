<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('freemius_pricings', function (Blueprint $table) {
            $table->id();
            // Freemius IDs
            $table->unsignedBigInteger('pricing_id')->unique(); // id:
            $table->foreignId('plan_id')->references('plan_id')
                ->on('freemius_plans')->onDelete('cascade');

            // License count
            $table->integer('licenses')->default(1);

            // Prices
            $table->decimal('monthly_price', 10, 2)->nullable();
            $table->decimal('annual_price', 10, 2)->nullable();
            $table->decimal('lifetime_price', 10, 2)->nullable();

            // Currency
            $table->string('currency', 10)->default('usd');

            // Flags
            $table->boolean('is_whitelabeled')->default(false);
            $table->boolean('is_hidden')->default(false);

            // Freemius timestamps
            $table->timestamp('freemius_created_at')->nullable();
            $table->timestamp('freemius_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_pricings');
    }
};
