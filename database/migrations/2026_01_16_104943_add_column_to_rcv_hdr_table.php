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
        Schema::table('rcv_hdr', function (Blueprint $table) {
            $table->dateTime('start_loading')->after('inspect_date')->nullable();
            $table->dateTime('finish_loading')->after('start_loading')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcv_hdr', function (Blueprint $table) {
            //
        });
    }
};
