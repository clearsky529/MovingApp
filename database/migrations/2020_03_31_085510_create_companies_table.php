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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->integer('tbl_users_id');
            $table->string('kika_id');
            $table->string('name');
            $table->string('email');
            $table->string('website');
            $table->string('contact_name');
            $table->string('contact_number',45)->nullable();
            $table->integer('city')->nullable();
            $table->integer('state');
            $table->integer('country');
            $table->integer('type');
            $table->integer('subscription_id')->nullable()->default(null);
            $table->integer('referred_by')->nullable()->default(null);
            $table->string('referral_code',250);
            $table->integer('free_trial_day')->nullable();
            $table->boolean('kika_direct')->comment('0 => kika_moving , 1 => kika_direct');
            $table->integer('created_by');
            $table->integer('modified_by');
            $table->integer('status')->default(null)->nullable();
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
        Schema::dropIfExists('companies');
    }
};
