<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('staff', function (Blueprint $table) {
        $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
public function down(): void
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('staff');
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}



};
