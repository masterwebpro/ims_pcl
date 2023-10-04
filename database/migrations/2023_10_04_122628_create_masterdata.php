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
        Schema::create('masterdata', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('store_id')->index();
            $table->unsignedInteger('warehouse_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->unsignedInteger('storage_location_id')->index();
            $table->string('item_type', 50); //good, damage, repair. etc
            $table->double('inv_qty', 8, 2)->default(0);
            $table->bigInteger('inv_uom');
            $table->double('whse_qty', 8, 2)->default(0);
            $table->bigInteger('whse_uom');
            $table->date('expiry_date')->nullable();
            $table->string('lot_no', 100)->nullable(); //good, damage, repair. etc
            $table->date('received_date', 50)->nullable(); //good, damage, repair. etc
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masterdata');
    }
};
