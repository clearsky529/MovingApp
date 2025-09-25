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
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->integer('status')->default(0)->comment('0 => deactive, 1 => active');
            $table->integer('created_by')->nullable();
            $table->integer('is_login');
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
        Schema::dropIfExists('company_users');
    }
};
