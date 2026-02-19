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
        Schema::create('freemius_licenses', function (Blueprint $table) {
            $table->id();
            // Freemius External IDs (Storing as strings to avoid precision issues with large IDs)
            $table->unsignedBigInteger('freemius_id')->unique();
            // Freemius user reference
            $table->unsignedBigInteger('fs_user_id');
            $table->string('product_id')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('plan_id')->nullable();
            $table->string('pricing_id')->nullable();

            // License Details
            $table->integer('quota')->default(0);
            $table->integer('activated')->default(0);
            $table->integer('activated_local')->default(0);
            $table->mediumText('secret_key');

            // Boolean Flags
            $table->boolean('is_free_localhost')->default(false);
            $table->boolean('is_block_features')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_whitelabeled')->default(false);

            // Settings/Enum style values
            $table->integer('environment')->default(0);
            $table->integer('source')->default(0);

            // Date Times
            $table->timestamp('expiration')->nullable();
            $table->timestamp('freemius_created_at')->nullable();
            $table->timestamp('freemius_updated_at')->nullable();
            $table->timestamps();
            $table->index('fs_user_id');
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_licenses');
    }
};
