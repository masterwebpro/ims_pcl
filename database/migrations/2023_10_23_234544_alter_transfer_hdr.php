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
    public function up()
    {
        Schema::table('transfer_hdr', function (Blueprint $table) {
            $table->renameColumn('old_company_id', 'source_company_id');
            $table->renameColumn('old_store_id', 'source_store_id');
            $table->string('dr_no', 50)->nullable();
            $table->date('trans_date', 50);
            $table->string('requested_by', 150);
            $table->dropColumn('new_company_id');
            $table->dropColumn('new_store_id');
            $table->dropColumn('new_warehouse_id');
            $table->dropColumn('old_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
