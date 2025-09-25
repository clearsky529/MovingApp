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
        Schema::create('move_items', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->integer('item_id')->nullable();
            $table->string('item')->nullable()->default(null);
            $table->integer('screening_category_id')->nullable();
            $table->integer('packer_id')->nullable();
            $table->integer('item_number');
            $table->boolean('is_delivered')->default(0)->comment('0 => not delivered, 1 => delivered');
            $table->boolean('is_unpacked')->nullable()->comment('0 => packed, 1 => unpacked');
            $table->boolean('is_overflow')->default(0)->comment('0 => nonoverflow, 1 => overflow');
            $table->string('move_type')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('move_items');
    }
};
