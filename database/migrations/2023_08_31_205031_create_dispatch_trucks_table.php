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
        Schema::create('dispatch_truck', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_no',15)->index();
            $table->string('truck_type')->index();
            $table->decimal('no_of_package',12,2)->default(0);
            $table->string('plate_no')->nullable();
            $table->string('driver')->nullable();
            $table->string('contact')->nullable();
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
        Schema::dropIfExists('dispatch_truck');
    }
};
