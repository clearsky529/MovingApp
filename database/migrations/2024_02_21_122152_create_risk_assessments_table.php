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
        Schema::create('risk_assessments', function (Blueprint $table) {

            $table->id();
            $table->index('id');
            $table->integer('move_id');
            $table->index('move_id');
            $table->integer('move_type')->comment('1 => Uplift, 5 => Delivery');
            $table->string('team_leader', 255);
            $table->text('risk_comment')->nullable();
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
        Schema::dropIfExists('risk_assessments');
    }
};
