<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE academic_year_branch DROP FOREIGN KEY academic_year_branch_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_course DROP FOREIGN KEY academic_year_course_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_teacher DROP FOREIGN KEY academic_year_teacher_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_resource DROP FOREIGN KEY academic_year_resource_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_years MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE academic_year_branch ADD CONSTRAINT academic_year_branch_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_course ADD CONSTRAINT academic_year_course_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_teacher ADD CONSTRAINT academic_year_teacher_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_resource ADD CONSTRAINT academic_year_resource_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE academic_year_branch DROP FOREIGN KEY academic_year_branch_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_course DROP FOREIGN KEY academic_year_course_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_teacher DROP FOREIGN KEY academic_year_teacher_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_year_resource DROP FOREIGN KEY academic_year_resource_academic_year_id_foreign');
        DB::statement('ALTER TABLE academic_years MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE academic_year_branch ADD CONSTRAINT academic_year_branch_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_course ADD CONSTRAINT academic_year_course_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_teacher ADD CONSTRAINT academic_year_teacher_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE academic_year_resource ADD CONSTRAINT academic_year_resource_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
