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
        Schema::create('masterfiles', function (Blueprint $table) {
            $table->bigIncrements('masterfile_id');
            $table->string('ref_no','100')->nullable();
            $table->bigInteger('product_id');
            $table->string('item_type', 50); //good, damage, repair. etc
            $table->double('inv_qty', 8, 2)->default(0);
            $table->bigInteger('inv_uom');
            $table->double('whse_qty', 8, 2)->default(0);
            $table->bigInteger('whse_uom');
            $table->bigInteger('client_id');
            $table->bigInteger('store_id');
            $table->string('rack')->default('00');
            $table->string('level')->default('00');
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
        Schema::dropIfExists('masterfiles');
    }
};
