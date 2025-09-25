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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('company_type');
            $table->integer('currency_id')->nullable();
            $table->decimal('monthly_price')->nullable();
            $table->decimal('addon_price')->nullable();
            $table->integer('monthly_max_moves')->nullable();
            $table->integer('free_users')->nullable();
            $table->integer('status')->comment('0 => de-active, 1 => active');
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
        Schema::dropIfExists('subscriptions');
    }
};
