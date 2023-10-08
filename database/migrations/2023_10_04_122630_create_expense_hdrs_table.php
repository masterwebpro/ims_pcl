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
        Schema::create('expense_hdr', function (Blueprint $table) {
            $table->id();
            $table->string('expense_no',15)->index();
            $table->date('expense_date');
            $table->string('plate_no');
            $table->dateTime('posted_date');
            $table->bigInteger('posted_by')->nullable();
            $table->string('status')->index()->comment('open, posted');
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
        Schema::dropIfExists('expense_hdr');
    }
};
