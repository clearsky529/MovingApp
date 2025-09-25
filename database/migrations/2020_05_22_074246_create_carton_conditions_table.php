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
        Schema::create('carton_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('condition');
            $table->string('condition_code');
            $table->string('move_stage');
            $table->string('color_code');
            $table->boolean('is_side_required')->comment('0 => not required, 1 => required');
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
        Schema::dropIfExists('carton_conditions');
    }
};
