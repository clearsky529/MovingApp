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
        Schema::create('risk_assessment_details', function (Blueprint $table) {

            $table->id();
            $table->index('id');
            $table->unsignedBigInteger('risk_assessment_id');
            $table->foreign('risk_assessment_id')->references('id')->on('risk_assessments')->onDelete('cascade');
            $table->unsignedBigInteger('risk_title_id');
            $table->foreign('risk_title_id')->references('id')->on('risk_titles')->onDelete('cascade');
            $table->boolean('risk_priority')->comment('1 => Low , 2 => Medium , 3 => High ');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assessment_details');
    }
};
