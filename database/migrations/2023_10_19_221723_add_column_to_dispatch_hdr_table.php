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
        Schema::table('dispatch_hdr', function (Blueprint $table) {
            $table->dateTime('start_picking_datetime')->after('depart_datetime');
            $table->dateTime('finish_picking_datetime')->after('start_picking_datetime');
            $table->dateTime('arrival_datetime')->after('finish_picking_datetime');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
