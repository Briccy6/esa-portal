<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Support\MySqlTestSetup;
use Tests\TestCase;

class RolloverAcademicYearMySqlTest extends TestCase
{
    use MySqlTestSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMySqlConnection();
    }

    public function test_it_rolls_over_a_new_academic_year_using_mysql_connection(): void
    {
        $newYearLabel = '2027-2028';

        if (AcademicYear::where('year', $newYearLabel)->exists()) {
            AcademicYear::where('year', $newYearLabel)->delete();
        }

        $existingCourseId = DB::connection('mysql')->table('courses')->value('id');
        $this->assertNotNull($existingCourseId, 'Expected at least one course record in MySQL database.');

        $exitCode = Artisan::call('academic-year:rollover', [
            'year' => $newYearLabel,
            'starting_date' => '2027-08-01',
            'expiry_date' => '2028-07-31',
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
    }
}
