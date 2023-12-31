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
        Schema::table('attribute_entities', function (Blueprint $table) {
            $table->string('attribute_entity_default_value')->nullable()->after('attribute_entity_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_entities', function (Blueprint $table) {
            $table->dropColumn('attribute_entity_default_value')->nullable();
        });
    }
};
