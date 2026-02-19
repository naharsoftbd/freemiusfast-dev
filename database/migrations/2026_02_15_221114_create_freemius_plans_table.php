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
        Schema::create('freemius_plans', function (Blueprint $table) {
            $table->id();
            // Freemius IDs
            $table->unsignedBigInteger('plan_id')->unique(); // id: 39394
            $table->foreignId('plugin_id')->references('freemius_product_id')
                ->on('freemius_settings')->onDelete('cascade');

            // Basic Info
            $table->string('name');
            $table->string('title');
            $table->text('description')->nullable();

            // Flags
            $table->boolean('is_free_localhost')->default(false);
            $table->boolean('is_block_features')->default(false);
            $table->boolean('is_block_features_monthly')->default(false);
            $table->boolean('is_https_support')->default(false);
            $table->boolean('is_require_subscription')->default(false);
            $table->boolean('is_success_manager')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden')->default(false);

            // License & Trial
            $table->integer('license_type')->default(0);
            $table->integer('trial_period')->nullable();

            // Support
            $table->string('support_kb')->nullable();
            $table->string('support_forum')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_skype')->nullable();

            // API timestamps (Freemius timestamps)
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
        Schema::dropIfExists('freemius_plans');
    }
};
