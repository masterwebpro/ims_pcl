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
        Schema::table('users', function (Blueprint $table) {
            $table->string("company", 100)->nullable()->after('status');
            $table->string("department", 100)->nullable()->after('company');
            $table->string("designation", 100)->nullable()->after('department');
            $table->string("mobile_no", 100)->nullable()->after('designation');
            $table->string("position", 100)->nullable()->after('designation');
            $table->integer("role_id")->nullable()->after('mobile_no');
            $table->boolen("is_active")->default(0)->after('role_id');
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
