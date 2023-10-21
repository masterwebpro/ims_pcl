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
        Schema::create('transfer_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no','100')->index();
            $table->string('status',50)->index();
            $table->unsignedInteger('old_company_id')->index();
            $table->unsignedInteger('old_store_id')->index();
            $table->unsignedInteger('old_warehouse_id')->index();
           
            $table->unsignedInteger('new_company_id')->index();
            $table->unsignedInteger('new_store_id')->index();
            $table->unsignedInteger('new_warehouse_id')->index();
           
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('transfer_hdr');
    }
};
