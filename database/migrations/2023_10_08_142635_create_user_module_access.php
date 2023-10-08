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
        Schema::create('user_module_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('permission_id')->index();
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
        Schema::dropIfExists('user_module_access');
    }
};
