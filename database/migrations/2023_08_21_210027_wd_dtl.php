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
        Schema::create('wd_dtl', function (Blueprint $table) {
            $table->id();
            $table->string('wd_no',15)->index();
            $table->unsignedInteger('masterfile_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->decimal('inv_qty',12,2)->default(0);
            $table->bigInteger('inv_uom');
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
        Schema::dropIfExists('wd_dtl');
    }
};
