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
        Schema::create('uplift_moves', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->string('volume');
            $table->string('uplift_address', 256);
            $table->string('origin_agent_kika_id')->nullable();
            $table->string('origin_agent');
            $table->string('origin_agent_email');
            $table->dateTime('date');
            $table->string('origin_contactor_kika_id')->nullable();
            $table->string('vehicle_registration', 90)->nullable();
            $table->string('container_number', 90)->nullable();
            $table->string('sub_contactor_kika_id')->nullable();
            $table->string('sub_contactor')->nullable();
            $table->string('sub_contactor_email')->nullable();        
            $table->text('note', 65535)->nullable();
            $table->boolean('is_icr_created')->default(0)->comment('0 => not created, 1 => created');
            $table->integer('item_count')->nullable();
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
        Schema::dropIfExists('uplift_moves');
    }
};
