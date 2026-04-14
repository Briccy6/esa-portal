<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetDefaultMarksOutOfTo100 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set all NULL marks_out_of to 100
        DB::table('marks')->whereNull('marks_out_of')->update(['marks_out_of' => 100]);
        
        // Alter the column to add default value of 100
        Schema::table('marks', function (Blueprint $table) {
            $table->integer('marks_out_of')->default(100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->integer('marks_out_of')->nullable()->change();
        });
    }
}
