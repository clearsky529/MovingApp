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
        Schema::create('item_labels', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->string('short_name')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('parent_item_id')->nullable()->default(0);
            $table->string('item_type');
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
        Schema::dropIfExists('item_labels');
    }
};
