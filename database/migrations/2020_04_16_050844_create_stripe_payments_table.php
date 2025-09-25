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
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('payment_id');
            $table->decimal('amount');
            $table->string('customer_name')->nullable()->default(null);
            $table->decimal('application_fee')->nullable()->default(null);
            $table->string('cancellation_reason')->nullable()->default(null);
            $table->string('client_secret')->nullable()->default(null);
            $table->string('description')->nullable()->default(null);
            $table->string('payment_method')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->text('response_array',65535)->nullable()->default(null);
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
        Schema::dropIfExists('stripe_payments');
    }
};
