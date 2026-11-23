<?php

namespace Tests\Unit;

use App\Services\IdentificationDocumentVerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IdentificationDocumentVerificationServiceTest extends TestCase
{
    public function test_it_approves_a_rwandan_national_id_when_its_number_matches_its_birth_year_and_sex(): void
    {
        $result = $this->verify('ID', '1200181234567890', $this->nationalIdResponse());

        $this->assertTrue($result['success']);
        $this->assertTrue($result['checks']['cross_field_math_valid']);
        $this->assertTrue($result['checks']['document_number_matches']);
    }

    public function test_it_rejects_a_national_id_when_the_birth_year_or_sex_digit_conflicts(): void
    {
        $response = $this->nationalIdResponse();
        $response['date_of_birth'] = '02/03/2002';

        $result = $this->verify('ID', '1200181234567890', $response);

        $this->assertFalse($result['success']);
        $this->assertContains('The 16-digit ID birth year does not match the printed Date of Birth.', $result['issues']);
        $this->assertFalse($result['checks']['cross_field_math_valid']);

    }

    public function test_it_rejects_a_national_id_when_the_sex_digit_conflicts(): void
    {
        $response = $this->nationalIdResponse();
        $response['sex'] = 'F';

        $result = $this->verify('ID', '1200181234567890', $response);

        $this->assertFalse($result['success']);
        $this->assertContains('The National ID sex digit does not match the printed sex.', $result['issues']);
        $this->assertFalse($result['checks']['cross_field_math_valid']);
    }

    public function test_it_rejects_an_expired_rwandan_passport(): void
    {
        $response = $this->passportResponse();
        $response['expiry_date'] = now('Africa/Kigali')->subDay()->format('d/m/Y');

        $result = $this->verify('Passport', 'PC123456', $response);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('The travel document expired on', implode(' ', $result['issues']));
        $this->assertFalse($result['checks']['is_not_expired']);
    }

    public function test_it_approves_a_valid_rwandan_passport(): void
    {
        $result = $this->verify('Passport', 'PC123456', $this->passportResponse());

        $this->assertTrue($result['success']);
        $this->assertTrue($result['checks']['is_not_expired']);
        $this->assertTrue($result['checks']['mrz_matches']);
    }

    public function test_it_approves_a_valid_rwandan_laissez_passer(): void
    {
        $response = $this->passportResponse();
        $response['document_type'] = 'RWANDA_LAISSEZ_PASSER';
        $response['document_number'] = 'LP123456';
        $response['type_code'] = 'PL';

        $result = $this->verify('Passport', 'LP123456', $response);

        $this->assertTrue($result['success']);
        $this->assertSame('RWANDA_LAISSEZ_PASSER', $result['verification_result']['document_type']);
    }

    public function test_it_rejects_a_passport_when_the_mrz_does_not_match_the_printed_fields(): void
    {
        $response = $this->passportResponse();
        $response['mrz_matches_printed_data'] = false;

        $result = $this->verify('Passport', 'PC123456', $response);

        $this->assertFalse($result['success']);
        $this->assertContains(
            'The complete MRZ is missing, cropped, or does not match the printed document details.',
            $result['issues']
        );
        $this->assertFalse($result['checks']['mrz_matches']);
    }

    private function verify(string $identificationType, string $documentNumber, array $response): array
    {
        config(['services.gemini.api_key' => 'test-key']);
        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => json_encode($response)]],
                    ],
                ]],
            ]),
        ]);

        return (new IdentificationDocumentVerificationService())->verify(
            UploadedFile::fake()->image('document.jpg'),
            $identificationType,
            $documentNumber
        );
    }

    private function nationalIdResponse(): array
    {
        return array_merge($this->baseResponse(), [
            'document_type' => 'RWANDA_NATIONAL_ID',
            'has_ghost_portrait' => true,
            'is_front_side' => true,
            'security_background_valid' => true,
            'signature_present' => true,
            'place_of_issue_present' => true,
            'full_name' => 'Test Student',
            'document_number' => '1200181234567890',
            'date_of_birth' => '02/03/2001',
            'sex' => 'M',
        ]);
    }

    private function passportResponse(): array
    {
        return array_merge($this->baseResponse(), [
            'document_type' => 'RWANDA_PASSPORT',
            'has_ghost_portrait' => true,
            'full_name' => 'Test Student',
            'document_number' => 'PC123456',
            'date_of_birth' => '02/03/2001',
            'sex' => 'M',
            'nationality' => 'RWANDAN',
            'country_code' => 'RWA',
            'type_code' => 'P',
            'place_of_birth' => 'Kigali',
            'issue_date' => now('Africa/Kigali')->subYear()->format('d/m/Y'),
            'expiry_date' => now('Africa/Kigali')->addYear()->format('d/m/Y'),
            'issuing_authority_valid' => true,
            'mrz_visible' => true,
            'mrz_matches_printed_data' => true,
        ]);
    }

    private function baseResponse(): array
    {
        return [
            'document_type' => 'UNKNOWN_OR_OTHER',
            'confidence' => 0.98,
            'is_clear_and_unblurred' => true,
            'all_edges_visible' => true,
            'is_direct_capture' => true,
            'is_screen_or_copy_of_copy' => false,
            'is_digitally_altered' => false,
            'has_clear_portrait' => true,
            'branding_valid' => true,
            'required_fields_present' => true,
            'has_ghost_portrait' => false,
            'is_front_side' => false,
            'security_background_valid' => false,
            'signature_present' => false,
            'place_of_issue_present' => false,
            'full_name' => '',
            'document_number' => '',
            'date_of_birth' => '',
            'sex' => '',
            'nationality' => '',
            'country_code' => '',
            'type_code' => '',
            'place_of_birth' => '',
            'issue_date' => '',
            'expiry_date' => '',
            'issuing_authority_valid' => false,
            'mrz_visible' => false,
            'mrz_matches_printed_data' => false,
        ];
    }
}
