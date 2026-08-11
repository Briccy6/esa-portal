<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Tests\TestCase;

class AcademicYearStatusTest extends TestCase
{
    public function test_it_resolves_status_from_the_current_date(): void
    {
        $year = new AcademicYear([
            'year' => '2024-2025',
            'starting_date' => '2024-09-01',
            'expiry_date' => '2025-07-31',
        ]);

        $this->assertSame('upcoming', $year->resolveStatus(Carbon::parse('2024-08-15')));
        $this->assertSame('active', $year->resolveStatus(Carbon::parse('2024-12-10')));
        $this->assertSame('ended', $year->resolveStatus(Carbon::parse('2025-08-01')));
    }
}
