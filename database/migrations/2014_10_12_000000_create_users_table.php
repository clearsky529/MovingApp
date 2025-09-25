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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->string('email');
            $table->string('username');
            $table->string('status')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('stripe_id')->comment('customerId');
            $table->boolean('kika_direct')->comment('0 => kika_moving , 1 => kika_direct');
            $table->rememberToken();
            $table->string('intent_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
