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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('msrp', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('supplier_price', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('srp', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('special_price', $precision = 12, $scale = 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('msrp', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('supplier_price', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('srp', $precision = 12, $scale = 2)->default(0)->change();
            $table->decimal('special_price', $precision = 12, $scale = 2)->default(0)->change();
        });
    }
};
