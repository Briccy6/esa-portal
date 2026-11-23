<?php

namespace Tests\Feature;

use App\Http\Controllers\StudentController;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StudentPhotoAutoApprovalTest extends TestCase
{
    public function test_profile_uploads_auto_approve_id_and_passport_photos_after_basic_checks(): void
    {
        Http::fake();

        $student = new class extends Student
        {
            public bool $saveCalled = false;

            public function save(array $options = []): bool
            {
                $this->saveCalled = true;

                return true;
            }
        };
        $student->forceFill([
            'id' => 1,
            'full_name' => 'Test Student',
        ]);
        Auth::guard('student')->setUser($student);

        $request = Request::create('/student/profile', 'POST', [
            'identification_type' => 'ID',
            'id_number' => '1200181234567890',
        ], [], [
            'id_photo' => UploadedFile::fake()->image('identity.jpg'),
            'passport_photo' => UploadedFile::fake()->image('passport.jpg', 700, 900),
        ]);

        app(StudentController::class)->updateProfile($request);

        $this->assertTrue($student->saveCalled);
        $this->assertSame('approved', $student->id_photo_verification_status);
        $this->assertSame('approved', $student->passport_photo_verification_status);
        $this->assertFalse($student->passport_photo_requires_manual_review);
        $this->assertNotNull($student->passport_photo_verified_at);
        $this->assertSame(
            'basic_image_validation',
            $student->passport_photo_verification_result['method']
        );
        Http::assertNothingSent();
    }
}
