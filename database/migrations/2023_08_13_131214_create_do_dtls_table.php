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
        Schema::create('do_dtl', function (Blueprint $table) {
            $table->id();
            $table->string('do_no',15)->index();
            $table->unsignedInteger('product_id');
            $table->decimal('inv_qty',12,2)->default(0);
            $table->bigInteger('inv_uom');
            $table->decimal('whse_qty',12,2)->default(0);
            $table->bigInteger('whse_uom');
            $table->decimal('unserve_qty',12,2)->default(0);
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
        Schema::dropIfExists('do_dtl');
    }
};
