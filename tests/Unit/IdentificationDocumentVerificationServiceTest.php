<?php

namespace Tests\Unit;

use App\Services\IdentificationDocumentVerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IdentificationDocumentVerificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_it_accepts_a_valid_national_id_upload_without_calling_gemini(): void
    {
        $result = $this->validate('ID', '1200181234567890');

        $this->assertTrue($result['success']);
        $this->assertSame('basic_upload_validation', $result['verification_result']['method']);
        $this->assertNull($result['confidence']);
        Http::assertNothingSent();
    }

    public function test_it_rejects_a_national_id_number_with_an_invalid_format(): void
    {
        $result = $this->validate('ID', '123456789012345');

        $this->assertFalse($result['success']);
        $this->assertContains('Enter a National ID number containing exactly 16 digits.', $result['issues']);
        Http::assertNothingSent();
    }

    public function test_it_rejects_a_national_id_with_an_unrecognized_holder_category(): void
    {
        $result = $this->validate('ID', '9200181234567890');

        $this->assertFalse($result['success']);
        $this->assertContains('The first National ID digit is not a recognized holder category.', $result['issues']);
        Http::assertNothingSent();
    }

    public function test_it_accepts_a_passport_number_after_basic_upload_checks(): void
    {
        $result = $this->validate('Passport', 'PC123456');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['checks']['document_number_format_valid']);
        Http::assertNothingSent();
    }

    public function test_it_rejects_identification_images_over_200_kb(): void
    {
        $result = app(IdentificationDocumentVerificationService::class)->validateUpload(
            UploadedFile::fake()->image('document.jpg')->size(201),
            'ID',
            '1200181234567890'
        );

        $this->assertFalse($result['success']);
        $this->assertContains('The identification image must be 200 KB or smaller.', $result['issues']);
        Http::assertNothingSent();
    }

    private function validate(string $identificationType, string $documentNumber): array
    {
        return app(IdentificationDocumentVerificationService::class)->validateUpload(
            UploadedFile::fake()->image('document.jpg'),
            $identificationType,
            $documentNumber
        );
    }
}
