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
        Schema::table('rcv_dtl', function (Blueprint $table) {
            $table->date('manufacture_date')->after('lot_no')->nullable();
            $table->unsignedBigInteger('po_dtl_id')->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcv_dtl', function (Blueprint $table) {
            //
        });
    }
};
