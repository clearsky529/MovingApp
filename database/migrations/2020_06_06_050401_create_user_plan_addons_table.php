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
        Schema::create('user_plan_addons', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('subscription_id');
            $table->integer('payment_id');
            $table->integer('user_addon')->nullable();
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
        Schema::dropIfExists('user_plan_addons');
    }
};
