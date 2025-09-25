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
        Schema::create('tnc_checked', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->integer('move_type');
            $table->integer('tnc_id');
            $table->boolean('is_checked')->comment('0 => not checked, 1 => checked');
            $table->boolean('move_status')->comment('0 => pre, 1 => post'); 
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
        Schema::dropIfExists('tnc_checked');
    }
};
