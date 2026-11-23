<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class IdentificationDocumentVerificationService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-flash-latest');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    public function verify(UploadedFile $file, string $identificationType, string $expectedDocumentNumber): array
    {
        if (!in_array($identificationType, ['ID', 'Passport'], true)) {
            return $this->rejected(['Choose National ID or Passport as the document type.']);
        }

        if ($file->getSize() > 200 * 1024) {
            return $this->rejected(['The identification image must be 200 KB or smaller.']);
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            return $this->rejected(['Upload the document as a clear JPG, JPEG, or PNG image.']);
        }

        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw new RuntimeException('Unable to read the uploaded identification image.');
        }

        $response = $this->callGeminiAPI($file->getMimeType(), base64_encode($contents));
        $result = $this->parseGeminiResponse($response);
        $issues = $this->validateResult($result, $identificationType, $expectedDocumentNumber);

        return [
            'success' => $issues === [],
            'verification_result' => $result,
            'checks' => $this->getChecks($result, $identificationType, $expectedDocumentNumber),
            'confidence' => (float) $result['confidence'],
            'issues' => $issues,
        ];
    }

    protected function callGeminiAPI(string $mimeType, string $base64Data): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey,
            ])
            ->withOptions([
                'verify' => env('VERIFY_SSL', true),
            ])
            ->post("{$this->endpoint}/{$this->model}:generateContent", [
                'contents' => [[
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Data,
                            ],
                        ],
                        ['text' => $this->getVerificationPrompt()],
                    ],
                ]],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $this->getResponseSchema(),
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException("Gemini identification verification failed with status {$response->status()}.");
        }

        $data = $response->json();
        if (!is_array($data)) {
            throw new RuntimeException('Gemini returned an invalid identification verification response.');
        }

        return $data;
    }

    protected function getVerificationPrompt(): string
    {
        return <<<'PROMPT'
You are verifying a student's physical Rwandan identity document from a direct photo or scan.
The selected document type is supplied separately. Do not approve screenshots, screen photographs,
photocopies of photocopies, visibly edited documents, cropped documents, or unreadable documents.
Do not guess unreadable text. Return empty strings for fields that cannot be read and false for
checks that cannot be confirmed. Set document_type to UNKNOWN_OR_OTHER if this is not a genuine,
recognizable Rwandan document.
Set is_direct_capture to true only for a direct photo or scan of the physical document. Set
is_screen_or_copy_of_copy to true for a screen image or a photocopy made from another photocopy.
Set is_clear_and_unblurred to false if blur, glare, or shadows obscure any text or security feature.

For a National ID, verify the FRONT only: Rwanda/Republic of Rwanda and Indangamuntu/National
Identity Card branding, flag at top left, coat of arms at top right, blue/green/yellow background,
main portrait on the left, faint secondary portrait on the right, and the required names, 16-digit
ID number, date of birth, sex, place of issue, and holder signature. Extract sex as M or F and dates
as DD/MM/YYYY. Set branding_valid only when the header, title, flag, and coat of arms are present;
set security_background_valid only when the expected colors and ghost portrait are visible;
set is_front_side only when this is the photo-bearing front.

For a Passport or Laissez-Passer, verify Rwanda/Republic/République du Rwanda branding and coat of
arms, a clear main portrait and ghost portrait, RWA country code, appropriate document type, names,
document number, RWANDAN or RWANDAISE nationality, date of birth, sex, place of birth, issue and
expiry dates, DGIE issuing authority, and both complete MRZ lines. Confirm the MRZ matches the
printed surname, given names, document number, and date of birth. Extract dates as DD/MM/YYYY.
Set issuing_authority_valid only when DGIE is shown and mrz_matches_printed_data only when all
specified printed fields match the visible MRZ.

Return every requested field. For fields not applicable to this document type, use an empty string
or false. The printed number must be transcribed exactly; software will compare it with the number
entered by the student and independently validate National ID digits and passport expiry.
PROMPT;
    }

    protected function getResponseSchema(): array
    {
        $properties = [
            'document_type' => [
                'type' => 'STRING',
                'enum' => ['RWANDA_NATIONAL_ID', 'RWANDA_PASSPORT', 'RWANDA_LAISSEZ_PASSER', 'UNKNOWN_OR_OTHER'],
            ],
            'confidence' => ['type' => 'NUMBER'],
            'is_clear_and_unblurred' => ['type' => 'BOOLEAN'],
            'all_edges_visible' => ['type' => 'BOOLEAN'],
            'is_direct_capture' => ['type' => 'BOOLEAN'],
            'is_screen_or_copy_of_copy' => ['type' => 'BOOLEAN'],
            'is_digitally_altered' => ['type' => 'BOOLEAN'],
            'has_clear_portrait' => ['type' => 'BOOLEAN'],
            'branding_valid' => ['type' => 'BOOLEAN'],
            'required_fields_present' => ['type' => 'BOOLEAN'],
            'has_ghost_portrait' => ['type' => 'BOOLEAN'],
            'is_front_side' => ['type' => 'BOOLEAN'],
            'security_background_valid' => ['type' => 'BOOLEAN'],
            'signature_present' => ['type' => 'BOOLEAN'],
            'place_of_issue_present' => ['type' => 'BOOLEAN'],
            'full_name' => ['type' => 'STRING'],
            'document_number' => ['type' => 'STRING'],
            'date_of_birth' => ['type' => 'STRING'],
            'sex' => ['type' => 'STRING'],
            'nationality' => ['type' => 'STRING'],
            'country_code' => ['type' => 'STRING'],
            'type_code' => ['type' => 'STRING'],
            'place_of_birth' => ['type' => 'STRING'],
            'issue_date' => ['type' => 'STRING'],
            'expiry_date' => ['type' => 'STRING'],
            'issuing_authority_valid' => ['type' => 'BOOLEAN'],
            'mrz_visible' => ['type' => 'BOOLEAN'],
            'mrz_matches_printed_data' => ['type' => 'BOOLEAN'],
        ];

        return [
            'type' => 'OBJECT',
            'properties' => $properties,
            'required' => array_keys($properties),
        ];
    }

    protected function parseGeminiResponse(array $response): array
    {
        $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!is_string($content) || $content === '') {
            throw new RuntimeException('Gemini returned no identification verification result.');
        }

        $result = json_decode($content, true);
        if (!is_array($result) || json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Gemini returned an unreadable identification verification result.');
        }

        foreach (array_keys($this->getResponseSchema()['properties']) as $field) {
            if (!array_key_exists($field, $result)) {
                throw new RuntimeException("Gemini omitted the {$field} verification field.");
            }
        }

        foreach ([
            'is_clear_and_unblurred',
            'all_edges_visible',
            'is_direct_capture',
            'is_screen_or_copy_of_copy',
            'is_digitally_altered',
            'has_clear_portrait',
            'branding_valid',
            'required_fields_present',
            'has_ghost_portrait',
            'is_front_side',
            'security_background_valid',
            'signature_present',
            'place_of_issue_present',
            'issuing_authority_valid',
            'mrz_visible',
            'mrz_matches_printed_data',
        ] as $field) {
            if (!is_bool($result[$field])) {
                throw new RuntimeException("Gemini returned an invalid {$field} verification field.");
            }
        }

        foreach ([
            'document_type',
            'full_name',
            'document_number',
            'date_of_birth',
            'sex',
            'nationality',
            'country_code',
            'type_code',
            'place_of_birth',
            'issue_date',
            'expiry_date',
        ] as $field) {
            if (!is_string($result[$field])) {
                throw new RuntimeException("Gemini returned an invalid {$field} field.");
            }
        }

        if (!is_numeric($result['confidence']) || $result['confidence'] < 0 || $result['confidence'] > 1) {
            throw new RuntimeException('Gemini returned an invalid confidence score.');
        }

        return $result;
    }

    protected function validateResult(array $result, string $identificationType, string $expectedDocumentNumber): array
    {
        $issues = [];

        if (!$result['is_clear_and_unblurred']) {
            $issues[] = 'The document text is blurry or unreadable. Upload a sharper image.';
        }
        if (!$result['all_edges_visible']) {
            $issues[] = 'All four edges of the document must be visible and uncropped.';
        }
        if (!$result['is_direct_capture'] || $result['is_screen_or_copy_of_copy']) {
            $issues[] = 'Upload a direct photo or scan of the physical document, not a screen image or photocopy.';
        }
        if ($result['is_digitally_altered']) {
            $issues[] = 'The document appears digitally altered and could not be verified.';
        }
        if (!$result['has_clear_portrait']) {
            $issues[] = 'The document must show a clear, unobstructed portrait.';
        }
        if (!$result['branding_valid']) {
            $issues[] = 'Rwandan document branding or required national symbols could not be verified.';
        }
        if (!$result['required_fields_present']) {
            $issues[] = 'One or more required fields are missing or unreadable on the document.';
        }

        if ($identificationType === 'ID') {
            $this->validateNationalId($result, $expectedDocumentNumber, $issues);
        } else {
            $this->validateTravelDocument($result, $expectedDocumentNumber, $issues);
        }

        return array_values(array_unique($issues));
    }

    protected function validateNationalId(array $result, string $expectedDocumentNumber, array &$issues): void
    {
        if ($result['document_type'] !== 'RWANDA_NATIONAL_ID') {
            $issues[] = 'The uploaded image is not recognized as a Rwandan National ID front.';
        }
        if (!$result['is_front_side']) {
            $issues[] = 'You uploaded the back of the National ID; please upload the front side containing the photo and ID number.';
        }
        if (!$result['has_ghost_portrait'] || !$result['security_background_valid']) {
            $issues[] = 'The National ID portrait or background security features could not be verified.';
        }
        if (!$result['signature_present']) {
            $issues[] = 'The National ID holder signature is missing or unreadable.';
        }
        if (!$result['place_of_issue_present']) {
            $issues[] = 'The National ID place of issue is missing or unreadable.';
        }
        if (trim($result['full_name']) === '') {
            $issues[] = 'The name on the National ID could not be read.';
        }

        $number = preg_replace('/\s+/', '', $result['document_number']);
        $expectedNumber = preg_replace('/\s+/', '', $expectedDocumentNumber);
        if (!preg_match('/^\d{16}$/', $number)) {
            $issues[] = 'The National ID number must be exactly 16 digits.';
            return;
        }
        if (!preg_match('/^\d{16}$/', $expectedNumber)) {
            $issues[] = 'Enter a National ID number containing exactly 16 digits.';
        } elseif ($number !== $expectedNumber) {
            $issues[] = 'The National ID number on the image does not match the number entered in the profile.';
        }
        if (!in_array($number[0], ['1', '2', '3'], true)) {
            $issues[] = 'The first National ID digit is not a recognized holder category.';
        }

        $dateOfBirth = $this->parseDate($result['date_of_birth']);
        if ($dateOfBirth === null) {
            $issues[] = 'The National ID date of birth is missing or is not in DD/MM/YYYY format.';
        } elseif (substr($number, 1, 4) !== $dateOfBirth->format('Y')) {
            $issues[] = 'The 16-digit ID birth year does not match the printed Date of Birth.';
        }

        $sex = strtoupper(trim($result['sex']));
        if (!in_array($sex, ['M', 'F'], true)) {
            $issues[] = 'The National ID sex field could not be read as M or F.';
        } elseif (($number[5] === '8' && $sex !== 'M') || ($number[5] === '7' && $sex !== 'F')) {
            $issues[] = 'The National ID sex digit does not match the printed sex.';
        } elseif (!in_array($number[5], ['7', '8'], true)) {
            $issues[] = 'The National ID sex digit is not a recognized value.';
        }
    }

    protected function validateTravelDocument(array $result, string $expectedDocumentNumber, array &$issues): void
    {
        $isPassport = $result['document_type'] === 'RWANDA_PASSPORT';
        $isLaissezPasser = $result['document_type'] === 'RWANDA_LAISSEZ_PASSER';
        if (!$isPassport && !$isLaissezPasser) {
            $issues[] = 'The uploaded image is not recognized as a Rwandan passport or Laissez-Passer.';
        }
        if (!$result['has_ghost_portrait']) {
            $issues[] = 'The secondary portrait security feature could not be verified.';
        }
        if (strtoupper(trim($result['country_code'])) !== 'RWA') {
            $issues[] = 'The document country code must be RWA.';
        }
        if ($isPassport && strtoupper(trim($result['type_code'])) !== 'P') {
            $issues[] = 'The document type code is not valid for a passport.';
        }
        if ($isLaissezPasser && !in_array(strtoupper(trim($result['type_code'])), ['PL', 'LP', 'L', 'TD'], true)) {
            $issues[] = 'The document type code is not valid for a Laissez-Passer.';
        }
        if (!in_array(strtoupper(trim($result['nationality'])), ['RWANDAN', 'RWANDAISE'], true)) {
            $issues[] = 'The document nationality is missing or is not Rwandan.';
        }
        if (trim($result['full_name']) === '' || trim($result['place_of_birth']) === '') {
            $issues[] = 'The document name or place of birth is missing or unreadable.';
        }
        if (!in_array(strtoupper(trim($result['sex'])), ['M', 'F'], true)) {
            $issues[] = 'The document sex field could not be read as M or F.';
        }
        if (!$result['issuing_authority_valid']) {
            $issues[] = 'The Directorate General of Immigration and Emigration issuing authority could not be verified.';
        }
        if (!$result['mrz_visible'] || !$result['mrz_matches_printed_data']) {
            $issues[] = 'The complete MRZ is missing, cropped, or does not match the printed document details.';
        }

        $number = $this->normalizeDocumentNumber($result['document_number']);
        $expectedNumber = $this->normalizeDocumentNumber($expectedDocumentNumber);
        if ($number === '' || $number !== $expectedNumber) {
            $issues[] = 'The document number on the image does not match the number entered in the profile.';
        }
        if ($this->parseDate($result['date_of_birth']) === null) {
            $issues[] = 'The date of birth is missing or is not in DD/MM/YYYY format.';
        }
        if ($this->parseDate($result['issue_date']) === null) {
            $issues[] = 'The date of issue is missing or is not in DD/MM/YYYY format.';
        }

        $expiryDate = $this->parseDate($result['expiry_date']);
        if ($expiryDate === null) {
            $issues[] = 'The expiry date is missing or is not in DD/MM/YYYY format.';
        } elseif ($expiryDate < new DateTimeImmutable('today', new DateTimeZone('Africa/Kigali'))) {
            $issues[] = "The travel document expired on {$result['expiry_date']}.";
        }
    }

    protected function getChecks(array $result, string $identificationType, string $expectedDocumentNumber): array
    {
        $number = $identificationType === 'ID'
            ? preg_replace('/\s+/', '', $result['document_number'])
            : $this->normalizeDocumentNumber($result['document_number']);
        $expectedNumber = $identificationType === 'ID'
            ? preg_replace('/\s+/', '', $expectedDocumentNumber)
            : $this->normalizeDocumentNumber($expectedDocumentNumber);

        return [
            'is_clear_and_unblurred' => $result['is_clear_and_unblurred'],
            'all_edges_visible' => $result['all_edges_visible'],
            'is_direct_capture' => $result['is_direct_capture'] && !$result['is_screen_or_copy_of_copy'],
            'cross_field_math_valid' => $identificationType !== 'ID'
                || $this->nationalIdMathIsValid($result, $number),
            'document_number_matches' => $number !== '' && $number === $expectedNumber,
            'is_not_expired' => $identificationType === 'ID'
                || (($expiryDate = $this->parseDate($result['expiry_date'])) !== null
                    && $expiryDate >= new DateTimeImmutable('today', new DateTimeZone('Africa/Kigali'))),
            'mrz_matches' => $identificationType === 'ID'
                || ($result['mrz_visible'] && $result['mrz_matches_printed_data']),
        ];
    }

    protected function nationalIdMathIsValid(array $result, string $number): bool
    {
        if (!preg_match('/^\d{16}$/', $number) || !in_array($number[0], ['1', '2', '3'], true)) {
            return false;
        }

        $dateOfBirth = $this->parseDate($result['date_of_birth']);
        $sex = strtoupper(trim($result['sex']));
        return $dateOfBirth !== null
            && substr($number, 1, 4) === $dateOfBirth->format('Y')
            && (($number[5] === '8' && $sex === 'M') || ($number[5] === '7' && $sex === 'F'));
    }

    protected function parseDate(string $value): ?DateTimeImmutable
    {
        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!d/m/Y', $value, new DateTimeZone('Africa/Kigali'));
        $errors = DateTimeImmutable::getLastErrors();
        if (
            $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $date->format('d/m/Y') !== $value
        ) {
            return null;
        }

        return $date;
    }

    protected function normalizeDocumentNumber(string $value): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
    }

    protected function rejected(array $issues): array
    {
        return [
            'success' => false,
            'verification_result' => null,
            'checks' => [],
            'confidence' => 0.0,
            'issues' => $issues,
        ];
    }
}
