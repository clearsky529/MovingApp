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
        Schema::create('company_agents', function (Blueprint $table) {
            $table->id();
            $table->string('kika_id')->nullable();
            $table->integer('company_id');
            $table->string('email');
            $table->string('company_name');
            $table->integer('company_type');
            $table->string('phone',20)->nullable();
            $table->integer('status')->comment('0 => deactive, 1 => active');
            $table->string('website')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_kika_direct')->nullable()->comment('0 => kika_moving , 1 => kika_direct');
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('company_agents');
    }
};
