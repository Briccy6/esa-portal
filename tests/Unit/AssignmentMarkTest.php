<?php

namespace Tests\Unit;

use App\Models\Mark;
use Tests\TestCase;

class AssignmentMarkTest extends TestCase
{
    public function test_assignment_marks_are_normalized_like_test_marks(): void
    {
        $mark = new Mark([
            'assessment_type' => 'assignment',
            'value' => 15,
            'marks_out_of' => 20,
        ]);

        $this->assertSame(75.0, $mark->normalized_value);
    }

    public function test_exam_marks_keep_their_existing_scale(): void
    {
        $mark = new Mark([
            'assessment_type' => 'exam',
            'value' => 75,
            'marks_out_of' => 100,
        ]);

        $this->assertSame(75, $mark->normalized_value);
    }
}
