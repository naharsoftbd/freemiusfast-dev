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
        Schema::create('freemius_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Developer Details
            $table->unsignedBigInteger('developer_id'); // 123456
            $table->string('developer_public_key');
            $table->string('developer_secret_key');
            $table->string('public_url');
            $table->string('base_url');
            $table->string('api_base_url');

            // Product Details
            $table->unsignedBigInteger('freemius_product_id')->unique(); // 123456
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('saas'); // saas, theme, etc.
            $table->string('icon')->nullable();
            $table->string('secret_key')->unique()->nullable();
            $table->string('public_key')->unique()->nullable();
            $table->string('api_token')->unique();

            // Policies
            $table->integer('money_back_period')->default(0); // Money-back guarantee in days.
            $table->enum('refund_policy', ['flexible', 'moderate', 'strict'])->default('strict');
            $table->integer('annual_renewals_discount')->default(0);
            $table->string('renewals_discount_type')->default('percentage');
            $table->integer('lifetime_license_proration_days')->default(0);

            // Settings Flags
            $table->boolean('is_pricing_visible')->default(true);
            $table->integer('accepted_payments')->default(0);
            $table->boolean('expose_license_key')->default(true);
            $table->boolean('enable_after_purchase_email_login_link')->default(true);

            $table->json('freemius_payload')->nullable();

            // Timestamps (Using standard Laravel names or Freemius names)
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_settings');
    }
};
