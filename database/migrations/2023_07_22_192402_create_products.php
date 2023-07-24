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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('product_code', 150)->index();
            $table->string('product_upc', 150)->nullable();
            $table->string('product_sku', 150)->nullable();
            $table->string('product_name', 200);
            $table->unsignedInteger('category_id')->nullable()->index();
            $table->unsignedInteger('subcat_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->boolean('is_enabled')->default(1);
            $table->unsignedInteger('created_by');

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
        Schema::dropIfExists('products');
    }
};
