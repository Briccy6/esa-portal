<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('babyeyis')) {
            return;
        }

        $foreignKeys = collect(DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?',
            ['babyeyis', 'FOREIGN KEY']
        ))->pluck('CONSTRAINT_NAME');

        Schema::table('babyeyis', function (Blueprint $table) use ($foreignKeys) {
            if ($foreignKeys->contains('babyeyis_created_by_foreign')) {
                $table->dropForeign(['created_by']);
            }

            if ($foreignKeys->contains('babyeyis_edited_by_foreign')) {
                $table->dropForeign(['edited_by']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Destructive rollback intentionally omitted.
    }
};
