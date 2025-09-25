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
        Schema::create('delivery_moves', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->string('volume');
            $table->string('delivery_address', 256);
            $table->string('delivery_agent_kika_id')->nullable();
            $table->string('delivery_agent');
            $table->string('delivery_agent_email');
            $table->dateTime('date');
            $table->string('delivery_contactor_kika_id')->nullable();
            $table->string('vehicle_registration', 90)->nullable();
            $table->string('container_number')->nullable();
            $table->string('sub_contactor_kika_id')->nullable();
            $table->string('sub_contactor')->nullable();        
            $table->string('sub_contactor_email')->nullable();        
            $table->integer('lp_package')->nullable()->default(null);        
            $table->integer('lp_carton')->nullable()->default(null);        
            $table->integer('status')->default(0)->comment('0 => pending, 1 => in-progress, 2 => completed');
            $table->text('note', 65535)->nullable();
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
        Schema::dropIfExists('delivery_moves');
    }
};
