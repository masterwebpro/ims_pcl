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
            $table->string('trucker_name',100)->nullable()->after('depart_datetime');
            $table->string('truck_type',100)->nullable()->after('trucker_name');
            $table->string('plate_no',50)->nullable()->after('truck_type');
            $table->string('seal_no',50)->nullable()->after('plate_no');
            $table->string('driver',100)->nullable()->after('seal_no');
            $table->string('contact_no',20)->nullable()->after('driver');
            $table->string('helper',100)->nullable()->after('contact_no');
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
