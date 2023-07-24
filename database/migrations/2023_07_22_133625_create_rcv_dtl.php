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
        Schema::create('rcv_dtl', function (Blueprint $table) {
            $table->id();
            $table->string('rcv_no',15)->index();
            $table->string('sku',50)->nullable();
            $table->string('gtin',15)->nullable();
            $table->string('upc_no',15)->nullable();
            $table->unsignedInteger('product_id');
            $table->text('item_desc');
            $table->integer('requested_qty');
            $table->integer('actual_qty');
            $table->decimal('amount_per_unit', 12, 2)->default(0);
            $table->decimal('net_price', 12, 2)->default(0);
            $table->decimal('actual_net_price', 12, 2)->default(0);
            $table->decimal('amount_per_pc', 12, 2)->default(0);
            $table->unsignedInteger('uom_id');
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
        Schema::dropIfExists('rcv_dtl');
    }
};
