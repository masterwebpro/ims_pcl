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
            $table->string('dispatch_by')->nullable()->after('dispatch_date');
            $table->dateTime('start_datetime')->nullable()->after('dispatch_by');
            $table->dateTime('finish_datetime')->nullable()->after('start_datetime');
            $table->dateTime('depart_datetime')->nullable()->after('finish_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatch_hdr', function (Blueprint $table) {
            $table->dropColumn('dispatch_by')->nullable();
            $table->dropColumn('start_datetime')->nullable();
            $table->dropColumn('finish_datetime')->nullable();
            $table->dropColumn('depart_datetime')->nullable();
        });
    }
};
