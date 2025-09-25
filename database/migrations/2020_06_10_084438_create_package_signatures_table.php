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
        Schema::create('package_signatures', function (Blueprint $table) {
            $table->id();
            $table->integer('move_id');
            $table->integer('move_type');
            $table->string('client_name')->nullable();
            $table->string('client_signature')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_signature')->nullable();
            $table->boolean('status')->comment('0 => pre, 1 => post');
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
        Schema::dropIfExists('package_signatures');
    }
};
