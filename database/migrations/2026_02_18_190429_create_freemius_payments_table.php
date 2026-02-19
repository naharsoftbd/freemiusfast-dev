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
        Schema::create('freemius_payments', function (Blueprint $table) {
            $table->id();
            // 🔹 Local user relation
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // 🔹 Freemius identifiers
            $table->unsignedBigInteger('fs_user_id'); // id
            $table->unsignedBigInteger('freemius_payment_id')->unique(); // id
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('license_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('pricing_id')->nullable();
            $table->unsignedBigInteger('plugin_id')->nullable();
            $table->string('user_card_id')->nullable();
            $table->string('bound_payment_id')->nullable();
            $table->string('external_id')->nullable();

            // 🔹 Financial
            $table->decimal('gross', 12, 2)->nullable();
            $table->decimal('gateway_fee', 12, 2)->nullable();
            $table->decimal('vat', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();

            $table->boolean('is_renewal')->default(false);
            $table->string('type')->nullable();

            // 🔹 Gateway
            $table->string('gateway')->nullable();
            $table->string('payment_method')->nullable();
            $table->integer('environment')->nullable();
            $table->integer('source')->nullable();

            // 🔹 Location
            $table->string('ip')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('zip_postal_code')->nullable();
            $table->string('vat_id')->nullable();
            $table->string('coupon_id')->nullable();

            // 🔹 Plan info (mapped)
            $table->string('plan_title')->nullable();
            $table->integer('quota')->nullable();

            // 🔹 URLs
            $table->string('invoice_url')->nullable();

            // 🔹 Freemius timestamps
            $table->timestamp('freemius_created_at')->nullable();
            $table->timestamp('freemius_updated_at')->nullable();

            $table->timestamps();

            // Optional indexes for performance
            $table->index('subscription_id');
            $table->index('license_id');
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_payments');
    }
};
