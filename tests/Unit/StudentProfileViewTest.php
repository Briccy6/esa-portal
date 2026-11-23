<?php

namespace Tests\Unit;

use App\Models\Student;
use Tests\TestCase;

class StudentProfileViewTest extends TestCase
{
    public function test_student_profile_renders_guided_sections_and_image_preview_controls(): void
    {
        $student = new Student();
        $student->forceFill([
            'id' => 1,
            'reg_number' => 'ESA-TEST-001',
            'full_name' => 'Test Student',
            'phone' => '0780000000',
            'email' => 'student@example.com',
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('students.profile'));

        $response->assertOk()
            ->assertSee('Your profile')
            ->assertSee('Personal details')
            ->assertSee('Identification')
            ->assertSee('Education details')
            ->assertSee('Account security')
            ->assertSee('data-preview-target="passportPhotoPreview"', false)
            ->assertSee('data-preview-target="idPhotoPreview"', false)
            ->assertSee('verify-identification-document')
            ->assertSee('Checking your identification document with AI...');
    }
}
