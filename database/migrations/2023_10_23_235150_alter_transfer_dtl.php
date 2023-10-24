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
        Schema::table('transfer_dtl', function (Blueprint $table) {
            $table->renameColumn('old_storage_location_id', 'source_storage_location_id');
            $table->renameColumn('old_item_type', 'source_item_type');
            $table->renameColumn('old_inv_qty', 'source_inv_qty');
            $table->renameColumn('old_inv_uom', 'source_inv_uom');
            $table->unsignedInteger('source_warehouse_id')->after('product_id');
            $table->renameColumn('new_storage_location_id', 'dest_storage_location_id');
            $table->renameColumn('new_item_type', 'dest_item_type');
            $table->renameColumn('new_inv_qty', 'dest_inv_qty');
            $table->renameColumn('new_inv_uom', 'dest_inv_uom');
            $table->unsignedInteger('dest_warehouse_id')->after('old_inv_uom');
            $table->dropColumn('master_id');

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
