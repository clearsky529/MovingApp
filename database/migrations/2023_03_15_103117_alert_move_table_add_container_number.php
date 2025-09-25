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
        Schema::table('moves', function (Blueprint $table) {
            $table->string('container_number', 255)->nullable()->after('is_email_optional');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('moves', function (Blueprint $table) {
            $table->dropColumn('container_number');
        });
    }
};
