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
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('foreign_controlling_agent')->nullable()->default(null);
            $table->integer('foreign_origin_contractor')->nullable()->default(null);
            $table->integer('foreign_origin_agent')->nullable()->default(null);
            $table->integer('foreign_destination_contractor')->nullable()->default(null);
            $table->integer('foreign_destination_agent')->nullable()->default(null);
            $table->string('move_number');
            $table->string('reference_number');
            $table->string('controlling_agent_kika_id')->nullable()->default(null);
            $table->string('controlling_agent');
            $table->string('controlling_agent_email');
            $table->string('origin_agent')->nullable();
            $table->string('destination_agent')->nullable();
            $table->boolean('is_origin_agent_kika')->nullable()->default(0)->comment('0 => false, 1 => true');
            $table->boolean('is_destination_agent_kika')->nullable()->default(0)->comment('0 => false, 1 => true');
            $table->boolean('required_storage')->default(0)->comment('0 => not required, 1 => required');
            $table->boolean('required_screening')->default(0)->comment('0 => not required, 1 => required');
            $table->boolean('is_seprated')->default(2)->comment('0 => not seprated, 1 => seprated');
            $table->integer('status')->comment('0 => Pending, 1 => In-progress, 2 => Completed');
            $table->integer('is_completed_icr_uplift')->default(0)->comment('0 => In-complete, 1 => complete');
            $table->integer('is_completed_icr_delivery')->default(0)->comment('0 => In-complete, 1 => complete');
            $table->integer('is_tnc_checked')->default(0)->comment('0 => unchacked, 1 => checked');
            $table->integer('is_dl_tnc_checked')->default(0)->comment('0 => unchacked, 1 => checked');
            $table->integer('is_transload_tnc_checked')->default(0)->comment('0 => unchacked, 1 => checked');
            $table->integer('type_id')->default(1);
            $table->text('note', 65535)->nullable();
            $table->boolean('archive_status')->default(0)->comment('0 => unarchive, 1 => archive');
            $table->boolean('is_assign')->default(0);
            $table->boolean('is_overflow')->default(0)->comment('0 => nonoverflow, 1 => overflow');
            $table->integer('assign_destination_company_id');
            $table->boolean('is_email_optional')->default(0)->comment('0 => null, 1 => not null');
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('moves');
    }
};
