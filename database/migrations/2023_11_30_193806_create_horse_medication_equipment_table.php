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
        Schema::create('horse_medication_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horse_id');
            $table->unsignedBigInteger('medication_equipment_id');
            $table->timestamps();

            $table->foreign('horse_id')->references('id')->on('horses')->onDelete('cascade');
            $table->foreign('medication_equipment_id')->references('id')->on('medication_equipment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_medication_equipment');
    }
};
