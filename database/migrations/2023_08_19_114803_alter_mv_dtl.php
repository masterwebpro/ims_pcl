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
        Schema::table('mv_dtl', function (Blueprint $table) {
            $table->double('old_whse_qty', 8, 2)->default(0);
            $table->unsignedInteger('old_whse_uom');
            $table->double('new_whse_qty', 8, 2)->default(0);
            $table->unsignedInteger('new_whse_uom');
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
