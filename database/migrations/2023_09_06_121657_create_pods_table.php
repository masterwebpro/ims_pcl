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
        Schema::create('pod', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no',15)->index();
            $table->string('status')->index()->comment('open, posted');
            $table->dateTime('arrived_date')->nullable();
            $table->dateTime('depart_date')->nullable();
            $table->string('receive_by',50)->nullable();
            $table->dateTime('receive_date')->nullable();
            $table->string('dispatch_by')->nullable();
            $table->dateTime('dispatch_date')->nullable();
            $table->bigInteger('created_by');
            $table->text('remarks');
            $table->string('attachment')->nullable();
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
        Schema::dropIfExists('pod');
    }
};
