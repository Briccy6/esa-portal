<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->date('birthday');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('id_document');
            $table->string('result_slip');
            $table->string('passport_photo');


            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');

            $table->string('reg_number')->unique();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
