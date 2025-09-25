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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id');
            $table->integer('payment_id')->nullable()->default(null);
            $table->integer('user_id');
            $table->date('validity')->nullable()->default(null);
            $table->integer('currency_code')->nullable()->default(null);
            $table->decimal('subscription_price')->nullable()->default(null);
            $table->decimal('addon_unit_price')->nullable()->default(null);
            $table->integer('addon_user')->nullable()->default(null);
            $table->decimal('final_price')->nullable()->default(null);
            $table->decimal('total_icr_price')->nullable()->default(null);
            $table->boolean('is_expire_email_sent');
            $table->boolean('status')->comment('0 => in-active, 1 => active');
            $table->boolean('success_payment_status')->comment('0 => payment-incomplete, 1 => payment complete')->default(0);
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
        Schema::dropIfExists('user_subscriptions');
    }
};
