<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rcv_hdr', function (Blueprint $table) {
            $table->dropColumn(['total_requested_qty', 'total_actual_qty', 'total_unit_price']);
            $table->string('received_by',150);
        });
    }

    public function down()
    {
        //
    }
};
