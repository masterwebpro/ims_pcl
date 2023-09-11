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
        Schema::table('dispatch_truck', function (Blueprint $table) {
            $table->renameColumn('no_of_package','qty');
            $table->string('trucker_name')->nullable()->after('dispatch_no');
            $table->string('seal_no')->nullable()->after('plate_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatch_truck', function (Blueprint $table) {
            $table->renameColumn('no_of_package','qty');
            $table->dropColumn('trucker_name');
            $table->dropColumn('seal_no');
        });
    }
};
