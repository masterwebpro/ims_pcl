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
        Schema::create('rcv_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('rcv_no',15)->index();
            $table->string('po_num',100)->index();
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('supplier_id')->index();
            $table->datetime('date_received')->index();
            $table->datetime('po_date')->nullable();
            $table->string('inspect_by', 100);
            $table->date('inspect_date');
            $table->string('status')->index()->comment('open, posted');
            $table->unsignedInteger('total_requested_qty');
            $table->unsignedInteger('total_actual_qty');
            $table->decimal('total_unit_price', 12,2);
            $table->datetime('date_arrived');
            $table->datetime('date_departed');
            $table->string('plate_no', 50);
            $table->string('sales_invoice',100)->nullable()->index();
            $table->string('truck_type', 50);
            $table->unsignedInteger('warehouse_id');
            $table->string('remarks', 100)->nullable();
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
        Schema::dropIfExists('rcv_hdr');
    }
};
