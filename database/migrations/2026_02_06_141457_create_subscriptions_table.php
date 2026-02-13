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
            //$table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // It removed for hosted checkout
            $table->bigInteger('fs_user_id');
            $table->string('action');
            $table->decimal('amount', 8, 2);
            $table->integer('billing_cycle')->nullable();
            $table->char('currency', length: 3);
            $table->string('email');
            $table->dateTime('expiration')->nullable();
            $table->string('license_id');
            $table->integer('plan_id');
            $table->integer('pricing_id');
            $table->tinyInteger('quota');
            $table->integer('subscription_id')->nullable();
            $table->integer('payment_id')->nullable();
            $table->string('signature');
            $table->tinyInteger('tax');
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
