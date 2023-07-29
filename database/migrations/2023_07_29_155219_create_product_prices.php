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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->bigIncrements('product_price_id');
            $table->bigInteger('product_id');
            $table->decimal('msrp', $precision = 12, $scale = 2);
            $table->decimal('supplier_price', $precision = 12, $scale = 2);
            $table->decimal('srp', $precision = 12, $scale = 2);
            $table->decimal('special_price', $precision = 12, $scale = 2);
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
        Schema::dropIfExists('product_prices');
    }
};
