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
        Schema::table('storage_locations', function (Blueprint $table) {
            $table->dropColumn(['id', 'rcv_dtl_id']);
            //$table->bigIncrements('storage_location_id')->before('rack')->primary();
            $table->bigInteger('warehouse_id')->before('rack');
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
