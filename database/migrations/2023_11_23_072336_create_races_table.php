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
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('track_name');
            $table->timestamp('date')->comment('date of race');
            $table->integer('number_of_races')->comment('no of race on this track');
            $table->string('type');
            $table->unsignedBigInteger('age_id');
            $table->string('status')->comment('Search for the character "F". If "F" is present, then the race is "Filles/Mares Only"; else "Open"');
            $table->morphs('distance');
            $table->unsignedBigInteger('surface_id');
            $table->unsignedBigInteger('track_lookup_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('age_id')->references('id')->on('ages');
            $table->foreign('surface_id')->references('id')->on('surfaces');
            $table->foreign('track_lookup_id')->references('id')->on('track_lookups');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
