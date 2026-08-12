<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Support\MySqlTestSetup;
use Tests\TestCase;

class RolloverAcademicYearFallbackTest extends TestCase
{
    use MySqlTestSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMySqlConnection();
    }

    public function test_it_skips_teacher_associations_when_teacher_course_table_is_missing(): void
    {
        $newYearLabel = '2028-2029';

        if (AcademicYear::where('year', $newYearLabel)->exists()) {
            AcademicYear::where('year', $newYearLabel)->delete();
        }

        DB::statement('DROP TABLE IF EXISTS teacher_course');

        $existingCourseId = DB::connection('mysql')->table('courses')->value('id');
        $this->assertNotNull($existingCourseId, 'Expected at least one course record in MySQL database.');

        $exitCode = Artisan::call('academic-year:rollover', [
            'year' => $newYearLabel,
            'starting_date' => '2028-08-01',
            'expiry_date' => '2029-07-31',
        ]);

        $this->assertSame(0, $exitCode, Artisan::output());

        $newYear = AcademicYear::where('year', $newYearLabel)->first();
        $this->assertNotNull($newYear, 'Expected new academic year record to be created.');

        $this->assertDatabaseHas('academic_year_course', [
            'academic_year_id' => $newYear->id,
            'course_id' => $existingCourseId,
        ]);

        $this->assertDatabaseHas('academic_year_branch', [
            'academic_year_id' => $newYear->id,
        ]);

        $this->assertDatabaseHas('academic_year_resource', [
            'academic_year_id' => $newYear->id,
        ]);

        $this->assertDatabaseMissing('academic_year_teacher', [
            'academic_year_id' => $newYear->id,
        ]);
    }
}
