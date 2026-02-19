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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('plugin_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->unique(); // Freemius subscription ID
            $table->unsignedBigInteger('license_id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('pricing_id')->nullable();
            $table->string('plan_title');
            
            $table->decimal('renewal_amount', 10, 2)->default(0);
            $table->decimal('initial_amount', 10, 2)->default(0);
            $table->string('billing_cycle')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamp('renewal_date')->nullable();
            $table->string('currency', 10)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('freemius_created_at')->nullable();
            
            $table->text('checkout_upgrade_authorization')->nullable();
            $table->integer('quota')->nullable();
            
            $table->json('payment_method')->nullable(); // {"type": "card"}
            $table->string('upgrade_url')->nullable();
            
            $table->boolean('is_trial')->default(false);
            $table->timestamp('trial_ends')->nullable();
            $table->boolean('is_free_trial')->default(false);
            
            $table->string('apply_renewal_cancellation_coupon_url')->nullable();
            $table->string('cancel_renewal_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
