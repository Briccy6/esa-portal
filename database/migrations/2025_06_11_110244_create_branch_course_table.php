<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchCourseTable extends Migration
{
    public function up()
    {
         if (!Schema::hasTable('branch_course')) {
        Schema::create('branch_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
}

   public function down()
{
    Schema::dropIfExists('branch_course');
}

}
