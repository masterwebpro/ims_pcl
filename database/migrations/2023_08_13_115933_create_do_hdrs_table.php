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
        Schema::create('do_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('do_no',15)->index();
            $table->unsignedInteger('store_id')->index();
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('supplier_id')->index();
            $table->unsignedInteger('warehouse_id')->index();
            $table->string('po_num',100)->nullable()->index();
            $table->string('sales_invoice',100)->nullable()->index();
            $table->string('do_type',20)->index();
            $table->string('order_no',50)->index();
            $table->string('order_type',50);
            $table->date('order_date')->index();
            $table->date('pickup_date');
            $table->date('target_dlv_date');
            $table->date('actual_dlv_date');
            $table->string('status')->index()->comment('open, posted');
            $table->string('remarks',100)->nullable();
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('do_hdr');
    }
};
