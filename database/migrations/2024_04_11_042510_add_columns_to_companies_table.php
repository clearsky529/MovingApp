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
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('icr_title_toggle')->default(1)->comment('0 => toggle_unchecked , 1 => toggle_checked')->after('kika_direct');
            $table->string('icr_title_image')->nullable()->after('icr_title_toggle');
            $table->string('title_bar_color_code')->nullable()->after('icr_title_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
