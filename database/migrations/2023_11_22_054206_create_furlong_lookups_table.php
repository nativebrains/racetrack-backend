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
        Schema::create('furlong_lookups', function (Blueprint $table) {
            $table->id();
            $table->integer('distance')->unique();
            $table->string('type');
            $table->integer('value')->comment('this value is in furlong, i.e 200 is equal to 2 furlong');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('furlong_look_ups');
    }
};
