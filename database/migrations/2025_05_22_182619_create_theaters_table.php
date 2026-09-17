<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theaters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity');
            $table->string('screen_type')->default('standard'); // standard, imax, vip, etc.
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Create a default theater
        DB::table('theaters')->insert([
            'name' => 'Main Theater',
            'capacity' => 200,
            'screen_type' => 'standard',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add theater_id to showtimes table if it doesn't exist
        if (!Schema::hasColumn('showtimes', 'theater_id')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->foreignId('theater_id')->after('movie_id')->nullable();
            });

            // Assign all existing showtimes to the default theater
            DB::table('showtimes')->update(['theater_id' => 1]);

            // Now add the foreign key constraint
            Schema::table('showtimes', function (Blueprint $table) {
                $table->foreign('theater_id')->references('id')->on('theaters')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First remove the foreign key from showtimes
        if (Schema::hasColumn('showtimes', 'theater_id')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->dropForeign(['theater_id']);
                $table->dropColumn('theater_id');
            });
        }

        Schema::dropIfExists('theaters');
    }
};
