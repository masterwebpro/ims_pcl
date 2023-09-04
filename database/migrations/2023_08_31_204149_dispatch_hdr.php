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
        Schema::create('dispatch_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_no',15)->index();
            $table->date('dispatch_date')->index();
            $table->string('status')->index()->comment('open, posted');
            $table->bigInteger('created_by');
            $table->dateTime('posted_date');
            $table->bigInteger('posted_by');
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
        Schema::dropIfExists('dispatch_hdr');
    }
};
