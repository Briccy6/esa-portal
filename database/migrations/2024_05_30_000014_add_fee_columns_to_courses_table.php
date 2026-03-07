<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('courses', 'registration_fee')) {
                $table->decimal('registration_fee', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'tuition_fee')) {
                $table->decimal('tuition_fee', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'practical_fee')) {
                $table->decimal('practical_fee', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'trip_fee')) {
                $table->decimal('trip_fee', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'discount_fee')) {
                $table->decimal('discount_fee', 5, 2)->default(0.00)->comment('Discount percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $columns = ['academic_year_id', 'registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee', 'discount_fee'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
