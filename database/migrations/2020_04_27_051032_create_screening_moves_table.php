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
        Schema::create('screening_moves', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->string('volume');
            $table->integer('status')->default(0)->comment('0 => pending, 1 => in-progress, 2 => completed');
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
        Schema::dropIfExists('screening_moves');
    }
};
