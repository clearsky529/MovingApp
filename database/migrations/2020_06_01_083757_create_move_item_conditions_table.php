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
        Schema::create('move_item_conditions', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->integer('move_item_id');
            $table->integer('condition_id');
            $table->integer('move_type')->default(null)->nullable();
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
        Schema::dropIfExists('move_item_conditions');
    }
};
