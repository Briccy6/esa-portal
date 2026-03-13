<?php
// database/migrations/xxxx_xx_xx_create_staff_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    public function up()
    {
       if (!Schema::hasTable('staff')) {
Schema::create('staff', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('role');
    $table->string('phone');
    $table->string('password'); // hashed password
    $table->string('qr_code')->nullable(); 
    $table->string('photo')->nullable();
    $table->timestamps();
});
       }

    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
