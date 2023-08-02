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
        //
        Schema::table('rcv_dtl', function (Blueprint $table) {
            $table->dropColumn(['sku', 'gtin', 'upc_no', 'item_desc','requested_qty','actual_qty','amount_per_unit','net_price' ,'actual_net_price','amount_per_pc','uom_id']);
            $table->double('inv_qty', 8, 2)->after('product_id')->default(0);
            $table->bigInteger('inv_uom')->after('inv_qty');
            $table->double('whse_qty', 8, 2)->after('inv_uom')->default(0);
            $table->bigInteger('whse_uom')->after('whse_qty');
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
