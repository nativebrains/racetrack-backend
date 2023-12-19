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
        Schema::create('horses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('race_id');
            $table->string('track_name');
            $table->timestamp('date')->comment('date of race');
            $table->string('previous_race')->nullable()->comment('date of previous race');
            $table->string('name')->comment('horse name');
            $table->integer('weight_carried')->comment('weight carried by horse');
            $table->integer('age')->comment('horse age');
            $table->string('gender')->nullable()->comment('horse gender');
            $table->string('jockey')->nullable()->comment('Jockey name');
            $table->integer('win_odds')->comment('Win Odds (Net win per $1 bet - excludes the $1 bet, so 1.4 means 2.4 is returned)');
            $table->integer('claiming_price');
            $table->integer('finish_position')->comment('position at which horse finished the race');
            $table->string('trainer')->nullable()->comment('Trainer name');
            $table->string('owner')->nullable()->comment('Owner name');
            $table->json('data')->comment('save complete row as json');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('race_id')->references('id')->on('races')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourses');
    }
};
