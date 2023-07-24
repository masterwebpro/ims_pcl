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
        Schema::create('store_list', function (Blueprint $table) {
            $table->id();
            $table->string('store_code', 20)->index();
            $table->string('store_name', 100);
            $table->unsignedInteger('client_id')->index();
            $table->string('tin', 100)->nullable();
            $table->string('address_1', 100)->nullable();
            $table->string('address_2', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zipcode', 100)->nullable();
            $table->string('phone_no', 100)->nullable();
            $table->string('email_address', 100)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->boolean('is_vatable')->default(1);
            $table->boolean('is_enabled')->default(1);
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('store_list');
    }
};
