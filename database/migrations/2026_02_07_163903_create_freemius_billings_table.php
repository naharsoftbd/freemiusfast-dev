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
        Schema::create('freemius_billings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Freemius user reference
            $table->unsignedBigInteger('fs_user_id')->unique();

            // Identity
            $table->string('business_name')->nullable();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Tax
            $table->string('tax_id')->nullable();

            // Address
            $table->string('address_street')->nullable();
            $table->string('address_apt')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_country_code', 2)->nullable();

            // Freemius timestamps
            $table->timestamp('fs_created_at')->nullable();
            $table->timestamp('fs_updated_at')->nullable();

            // Laravel timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_billings');
    }
};
