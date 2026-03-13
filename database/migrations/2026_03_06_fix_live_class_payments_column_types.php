<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('live_class_payments', function (Blueprint $table) {
            // Fix user_type column - should be string, not bigint
            $table->string('user_type')->change();
            
            // Fix email column - should be string, not bigint
            $table->string('email')->change();
        });
    }

    public function down()
    {
        Schema::table('live_class_payments', function (Blueprint $table) {
            // Revert back to bigint (though this shouldn't be needed)
            $table->bigInteger('user_type')->unsigned()->change();
            $table->bigInteger('email')->unsigned()->change();
        });
    }
};
