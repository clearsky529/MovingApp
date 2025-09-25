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
        Schema::create('transload_moves', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->string('volume');
            $table->string('location');
            $table->string('category');
            $table->string('type');
            $table->boolean('movement')->nullable()->comment('0 => into store, 1 => out of store');
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
        Schema::dropIfExists('transload_moves');
    }
};
