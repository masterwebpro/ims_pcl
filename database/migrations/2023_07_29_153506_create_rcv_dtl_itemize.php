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
        Schema::create('rcv_dtl_itemize', function (Blueprint $table) {
            $table->bigIncrements('rcv_dtl_itemize_id');
            $table->bigInteger('rcv_dtl_id');
            $table->bigInteger('attribute_id');
            $table->string('item_value', 150);
            $table->boolean('is_available')->default(1);
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
        //
    }
};
