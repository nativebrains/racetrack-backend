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
        Schema::create('yard_lookups', function (Blueprint $table) {
            $table->id();
            $table->integer('distance')->unique();
            $table->string('type');
            $table->double('value')->comment('this value is in Yards, i.e 220 is equal to 1 furlong');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yard_look_ups');
    }
};
