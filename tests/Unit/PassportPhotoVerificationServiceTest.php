<?php

namespace Tests\Unit;

use App\Services\PassportPhotoVerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PassportPhotoVerificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_valid_passport_photo_upload_is_queued_for_manual_review_without_ai(): void
    {
        $result = app(PassportPhotoVerificationService::class)->validateForManualReview(
            UploadedFile::fake()->image('passport.jpg', 700, 900)
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['requires_manual_review']);
        $this->assertSame('basic_image_validation', $result['verification_result']['method']);
        $this->assertNull($result['confidence']);
        Http::assertNothingSent();
    }

    public function test_invalid_passport_photo_dimensions_are_rejected_without_ai(): void
    {
        $result = app(PassportPhotoVerificationService::class)->validateForManualReview(
            UploadedFile::fake()->image('passport.jpg', 500, 500)
        );

        $this->assertFalse($result['success']);
        $this->assertSame('INCORRECT_ASPECT_RATIO', $result['rejection_reason']);
        Http::assertNothingSent();
    }
}
