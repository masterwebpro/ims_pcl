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
        Schema::create('po_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('po_num', 100)->index();
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('supplier_id')->index();
            $table->unsignedInteger('po_date')->index();
            $table->string('status')->index();
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
        Schema::dropIfExists('po_hdr');
    }
};
