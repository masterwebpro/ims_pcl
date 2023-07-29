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
        Schema::create('attributes', function (Blueprint $table) {
            $table->bigIncrements('attribute_id');
            $table->string('attribute_code',30);
            $table->string('attribute_name',150);
            $table->string('attribute_input_type',150);
            $table->string('attribute_display_name',150);
            $table->boolean('is_required')->default(0);
            $table->boolean('is_enabled')->default(0);
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
        Schema::dropIfExists('attributes');
    }
};
