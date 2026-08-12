<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RolloverAcademicYearCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rolls_over_a_new_academic_year_with_associations(): void
    {
        $existingYear = AcademicYear::create([
            'year' => '2025-2026',
            'starting_date' => '2025-08-04',
            'expiry_date' => '2026-07-03',
            'is_ongoing' => true,
        ]);

        $teacherId = DB::table('teachers')->insertGetId([
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
        ]);

        $branchId = DB::table('branches')->insertGetId([
            'branch_name' => 'Main Branch',
        ]);

        $courseId = DB::table('courses')->insertGetId([
            'academic_year_id' => $existingYear->id,
            'course_name' => 'Mathematics',
            'abbreviation' => 'MATH',
            'course_type' => 'core',
            'course_code' => 'MATH101',
            'registration_fee' => 0,
            'tuition_fee' => 0,
            'practical_fee' => 0,
            'trip_fee' => 0,
            'discount_fee' => 0,
            'total_cost' => 0,
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => $teacherId,
            'course_id' => $courseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('branch_course')->insert([
            'branch_id' => $branchId,
            'course_id' => $courseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resourceId = DB::table('resources')->insertGetId([
            'teacher_id' => $teacherId,
            'course_id' => $courseId,
            'branch_id' => $branchId,
            'lesson_id' => 1,
            'youtube_link' => null,
            'document_path' => null,
            'price' => 0,
            'is_public' => 0,
            'is_free' => 1,
            'watermark_path' => null,
            'watermark_applied' => 0,
            'admin_notes' => null,
            'video_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Artisan::call('academic-year:rollover', [
            'year' => '2026-2027',
            'starting_date' => '2026-08-05',
            'expiry_date' => '2027-07-04',
        ]);

        $this->assertDatabaseHas('academic_years', ['year' => '2026-2027']);
        $newYear = AcademicYear::where('year', '2026-2027')->first();
        $this->assertNotNull($newYear);

        $this->assertDatabaseHas('academic_year_course', [
            'academic_year_id' => $newYear->id,
            'course_id' => $courseId,
        ]);

        $this->assertDatabaseHas('academic_year_teacher', [
            'academic_year_id' => $newYear->id,
            'teacher_id' => $teacherId,
        ]);

        $this->assertDatabaseHas('academic_year_branch', [
            'academic_year_id' => $newYear->id,
            'branch_id' => $branchId,
        ]);

        $this->assertDatabaseHas('academic_year_resource', [
            'academic_year_id' => $newYear->id,
            'resource_id' => $resourceId,
        ]);
    }
}
