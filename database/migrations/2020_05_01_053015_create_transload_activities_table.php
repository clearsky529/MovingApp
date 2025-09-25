<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transload_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->integer('transload_id');
            $table->string('from');
            $table->string('to');
            $table->dateTime('transload_date');
            $table->dateTime('complete_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transload_activities');
    }
};
