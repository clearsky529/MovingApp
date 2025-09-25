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
        Schema::create('carton_choices', function (Blueprint $table) {
            $table->id();
            $table->string('cartoon_choice');
            $table->string('cartoon_code');
            $table->string('comment');
            $table->string('item_type');
            $table->string('good_type')->nullable();
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
        Schema::dropIfExists('carton_choices');
    }
};
