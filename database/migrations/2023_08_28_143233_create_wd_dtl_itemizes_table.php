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
        Schema::create('wd_dtl_itemize', function (Blueprint $table) {
            $table->bigIncrements('wd_dtl_itemize_id');
            $table->bigInteger('wd_dtl_id')->index();
            $table->string('serial_no',50)->index();
            $table->string('warranty_no',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wd_dtl_itemize');
    }
};
