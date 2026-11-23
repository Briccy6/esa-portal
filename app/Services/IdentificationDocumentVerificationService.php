<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class IdentificationDocumentVerificationService
{
    public function validateUpload(UploadedFile $file, string $identificationType, string $expectedDocumentNumber): array
    {
        $issues = [];

        if (!in_array($identificationType, ['ID', 'Passport'], true)) {
            $issues[] = 'Choose National ID or Passport as the document type.';
        }

        if ($file->getSize() > 200 * 1024) {
            $issues[] = 'The identification image must be 200 KB or smaller.';
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            $issues[] = 'Upload the document as a clear JPG, JPEG, or PNG image.';
        }

        if ($identificationType === 'ID') {
            $number = preg_replace('/\s+/', '', $expectedDocumentNumber);

            if (!preg_match('/^\d{16}$/', $number)) {
                $issues[] = 'Enter a National ID number containing exactly 16 digits.';
            } elseif (!in_array($number[0], ['1', '2', '3'], true)) {
                $issues[] = 'The first National ID digit is not a recognized holder category.';
            }
        } elseif ($identificationType === 'Passport') {
            $number = preg_replace('/[^A-Z0-9]/i', '', $expectedDocumentNumber);

            if ($number === '') {
                $issues[] = 'Enter a passport or Laissez-Passer number.';
            }
        }

        if ($issues !== []) {
            return [
                'success' => false,
                'verification_result' => null,
                'checks' => [],
                'confidence' => null,
                'issues' => $issues,
            ];
        }

        return [
            'success' => true,
            'verification_result' => ['method' => 'basic_upload_validation'],
            'checks' => [
                'upload_valid' => true,
                'document_number_format_valid' => true,
            ],
            'confidence' => null,
            'issues' => [],
        ];
    }
}
