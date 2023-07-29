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
        Schema::create('attribute_entities', function (Blueprint $table) {
            $table->bigIncrements('attribute_entity_id');
            $table->bigInteger('attribute_id');
            $table->string('attribute_entity_value',150);
            $table->string('attribute_entity_name',150);
            $table->text('attribute_entity_description')->nullable();
            $table->boolean('is_default')->default(0);
            $table->integer('attribute_entity_position')->unsigned();
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
        Schema::dropIfExists('attribute_entities');
    }
};
