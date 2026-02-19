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
        Schema::create('freemius_plan_feature', function (Blueprint $table) {
            $table->id();
            // Relationship // Freemius plan ID
            $table->foreignId('plan_id')
                ->constrained('freemius_plans')
                ->cascadeOnDelete();

            $table->foreignId('feature_id')
                ->constrained('freemius_features')
                ->cascadeOnDelete();

            $table->string('value')->nullable();
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->unique(['plan_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freemius_plan_feature');
    }
};
