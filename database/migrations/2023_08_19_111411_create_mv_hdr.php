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
        Schema::create('mv_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no','100');
            $table->string('status',50);
            $table->bigInteger('client_id');
            $table->bigInteger('store_id');
            $table->bigInteger('warehouse_id');
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
        Schema::dropIfExists('mv_hdr');
    }
};
