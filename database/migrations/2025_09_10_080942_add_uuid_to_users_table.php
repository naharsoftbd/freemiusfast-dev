<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add nullable column first (no unique yet)
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Step 2: Fill UUIDs for existing records
        DB::table('users')->get()->each(function ($user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        // Step 3: Make column unique and not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
