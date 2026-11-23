<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PassportPhotoVerificationService
{
    public function validateForManualReview(UploadedFile $file): array
    {
        $validation = $this->validateImage($file);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'reason' => $validation['reason'],
                'rejection_reason' => $validation['reason'],
                'requires_manual_review' => false,
            ];
        }

        return [
            'success' => true,
            'verification_result' => ['method' => 'basic_image_validation'],
            'confidence' => null,
            'requires_manual_review' => true,
            'rejection_reason' => null,
        ];
    }

    protected function validateImage(UploadedFile $file): array
    {
        if ($file->getSize() > 2 * 1024 * 1024) {
            return ['valid' => false, 'reason' => 'FILE_TOO_LARGE'];
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'], true)) {
            return ['valid' => false, 'reason' => 'INVALID_FILE_TYPE'];
        }

        try {
            $image = (new ImageManager(new Driver()))->read($file->getRealPath());
            $width = $image->width();
            $height = $image->height();

            if ($width < 300 || $height < 385) {
                return ['valid' => false, 'reason' => 'IMAGE_TOO_SMALL'];
            }

            $expectedRatio = 7 / 9;
            if (abs(($width / $height) - $expectedRatio) > $expectedRatio * 0.2) {
                return ['valid' => false, 'reason' => 'INCORRECT_ASPECT_RATIO'];
            }

            if ($width > $height) {
                return ['valid' => false, 'reason' => 'LANDSCAPE_ORIENTATION'];
            }
        } catch (Exception $exception) {
            Log::warning('Passport photo image validation failed', [
                'error' => $exception->getMessage(),
            ]);

            return ['valid' => false, 'reason' => 'INVALID_IMAGE'];
        }

        return ['valid' => true];
    }

    public static function getErrorMessage(string $reason): string
    {
        $messages = [
            'FILE_TOO_LARGE' => 'Your photograph exceeds the maximum allowed size of 2 MB.',
            'INVALID_FILE_TYPE' => 'Only JPG, JPEG and PNG photographs are accepted.',
            'IMAGE_TOO_SMALL' => 'Your photograph is too small. Minimum resolution is 300 × 385 pixels.',
            'INCORRECT_ASPECT_RATIO' => 'Your photograph has an incorrect aspect ratio. It should be approximately 7:9.',
            'LANDSCAPE_ORIENTATION' => 'Your image must be in portrait orientation.',
            'INVALID_IMAGE' => 'The uploaded file is not a valid image.',
            'NO_FACE' => 'No human face was detected. Please upload a clear passport-style photograph.',
            'MULTIPLE_FACES' => 'More than one person was detected. Please upload a photograph showing only yourself.',
            'GROUP_PHOTO' => 'Group photographs are not accepted. Please upload a photograph showing only yourself.',
            'COLORED_BACKGROUND' => 'Your photograph has a colored or unsuitable background. Please upload a passport photograph with a plain white background.',
            'SELFIE' => 'This photograph does not meet our passport photo requirements. Please upload a professional passport-style photograph.',
            'DOCUMENT_SCAN' => 'Scanned identity cards and documents are not accepted. Please upload a passport-style portrait photograph.',
            'BLURRY_IMAGE' => 'Your photograph is blurry. Please upload a clearer image.',
            'NOT_FACING_CAMERA' => 'The person in the photograph is not facing the camera directly.',
            'FACE_NOT_CENTERED' => 'The face is not centered in the photograph.',
            'INCORRECT_ORIENTATION' => 'Your image must be in portrait orientation.',
            'GENERAL_REJECTION' => 'Your photograph does not meet our passport photo requirements.',
        ];

        return $messages[$reason] ?? 'Your photograph does not meet our passport photo requirements.';
    }
}
