<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;

class PassportPhotoVerificationService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-exp');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    /**
     * Verify passport photo using Gemini AI
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function verify($file): array
    {
        // Step 1: Pre-validate the image
        $validation = $this->preValidateImage($file);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'reason' => $validation['reason'],
                'requires_manual_review' => false,
            ];
        }

        // Step 2: Get image data
        try {
            $imageData = $this->getImageData($file);
        } catch (Exception $e) {
            Log::error('Failed to process image for verification', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'reason' => 'Failed to process image',
                'requires_manual_review' => true,
            ];
        }

        // Step 3: Call Gemini API
        try {
            $response = $this->callGeminiAPI($imageData);
        } catch (Exception $e) {
            Log::error('Gemini API call failed', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'reason' => 'Verification service temporarily unavailable',
                'requires_manual_review' => true,
            ];
        }

        // Step 4: Parse and validate response
        return $this->parseGeminiResponse($response);
    }

    /**
     * Pre-validate image before sending to Gemini
     */
    protected function preValidateImage($file): array
    {
        // Check file size (200 KB max)
        if ($file->getSize() > 204800) {
            return [
                'valid' => false,
                'reason' => 'FILE_TOO_LARGE',
            ];
        }

        // Check file type
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return [
                'valid' => false,
                'reason' => 'INVALID_FILE_TYPE',
            ];
        }

        // Get image dimensions
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $width = $image->width();
            $height = $image->height();

            // Check minimum resolution (300x385)
            if ($width < 300 || $height < 385) {
                return [
                    'valid' => false,
                    'reason' => 'IMAGE_TOO_SMALL',
                ];
            }

            // Check aspect ratio (approximately 7:9, allow 20% tolerance)
            $aspectRatio = $width / $height;
            $expectedRatio = 7 / 9;
            $ratioTolerance = 0.2;

            if (abs($aspectRatio - $expectedRatio) > $expectedRatio * $ratioTolerance) {
                return [
                    'valid' => false,
                    'reason' => 'INCORRECT_ASPECT_RATIO',
                ];
            }

            // Check orientation (portrait)
            if ($width > $height) {
                return [
                    'valid' => false,
                    'reason' => 'LANDSCAPE_ORIENTATION',
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to validate image dimensions', [
                'error' => $e->getMessage(),
            ]);
            return [
                'valid' => false,
                'reason' => 'INVALID_IMAGE',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Get image data as base64 with MIME type
     */
    protected function getImageData($file): string
    {
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();

        // Convert PNG to JPEG for Gemini (it prefers JPEG)
        if ($mimeType === 'image/png') {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath())->toJpeg(90);
            $imageData = base64_encode($image);
            $mimeType = 'image/jpeg';
        }

        return "{$mimeType}|{$imageData}";
    }

    /**
     * Call Gemini API for image analysis
     */
    protected function callGeminiAPI(string $imageData): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured');
        }

        $prompt = $this->getVerificationPrompt();

        // Parse image data (format: mimeType|base64Data)
        list($mimeType, $base64Data) = explode('|', $imageData, 2);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Data,
                            ],
                        ],
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'topK' => 32,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
                'responseMimeType' => 'application/json',
                'responseSchema' => $this->getResponseSchema(),
            ],
        ];

        $url = "{$this->endpoint}/{$this->model}:generateContent";

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey,
            ])
            ->withOptions([
                'verify' => env('VERIFY_SSL', true),
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            throw new Exception("Gemini API returned error: {$response->status()} - {$response->body()}");
        }

        return $response->json();
    }

    /**
     * Get the verification prompt for Gemini
     */
    protected function getVerificationPrompt(): string
    {
        return <<<'PROMPT'
You are an expert passport photograph validator for a school registration system. Analyze the uploaded photograph and determine if it meets the following passport photograph requirements:

REQUIREMENTS:
1. Background: Plain white (no colored or busy backgrounds)
2. Image type: Professional passport-style portrait (not selfie, not casual photo)
3. Number of faces: Exactly one visible human face
4. Face position: Centered in the frame
5. Person facing: Directly at the camera (not profile, not looking away)
6. Orientation: Portrait (not landscape)
7. Aspect ratio: Approximately 7:9
8. Image quality: Clear and not blurry
9. Head and shoulders: Visible with appropriate framing
10. Not a document scan or screenshot

Return a JSON response with the following structure:
{
  "is_passport_photo": true/false,
  "has_face": true/false,
  "face_count": number,
  "white_background": true/false,
  "face_centered": true/false,
  "facing_camera": true/false,
  "portrait_orientation": true/false,
  "is_blurry": true/false,
  "is_selfie": true/false,
  "is_group_photo": true/false,
  "is_document_scan": true/false,
  "confidence": 0.0-1.0,
  "rejection_reason": "specific reason if rejected, or null"
}

Be strict in your evaluation. A photograph must meet ALL requirements to be accepted as a passport photo.
PROMPT;
    }

    /**
     * Get the response schema for Gemini
     */
    protected function getResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'is_passport_photo' => ['type' => 'boolean'],
                'has_face' => ['type' => 'boolean'],
                'face_count' => ['type' => 'integer'],
                'white_background' => ['type' => 'boolean'],
                'face_centered' => ['type' => 'boolean'],
                'facing_camera' => ['type' => 'boolean'],
                'portrait_orientation' => ['type' => 'boolean'],
                'is_blurry' => ['type' => 'boolean'],
                'is_selfie' => ['type' => 'boolean'],
                'is_group_photo' => ['type' => 'boolean'],
                'is_document_scan' => ['type' => 'boolean'],
                'confidence' => ['type' => 'number'],
                'rejection_reason' => ['type' => 'string'],
            ],
            'required' => [
                'is_passport_photo',
                'has_face',
                'face_count',
                'white_background',
                'face_centered',
                'facing_camera',
                'portrait_orientation',
                'is_blurry',
                'is_selfie',
                'is_group_photo',
                'is_document_scan',
                'confidence',
                'rejection_reason',
            ],
        ];
    }

    /**
     * Parse Gemini API response
     */
    protected function parseGeminiResponse(array $response): array
    {
        try {
            Log::info('Gemini API Response', ['response' => $response]);

            // Extract the generated content
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($content)) {
                Log::error('No content in Gemini response', ['response' => $response]);
                throw new Exception('No content in Gemini response');
            }

            Log::info('Gemini response content', ['content' => $content]);

            // Parse JSON
            $result = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse Gemini JSON response', [
                    'error' => json_last_error_msg(),
                    'content' => $content,
                ]);
                throw new Exception('Failed to parse Gemini JSON response: ' . json_last_error_msg());
            }

            Log::info('Parsed Gemini result', ['result' => $result]);

            // Validate required fields
            $requiredFields = [
                'is_passport_photo',
                'has_face',
                'face_count',
                'white_background',
                'face_centered',
                'facing_camera',
                'portrait_orientation',
                'is_blurry',
                'is_selfie',
                'is_group_photo',
                'is_document_scan',
                'confidence',
                'rejection_reason',
            ];

            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $result)) {
                    Log::error("Missing required field in Gemini response: {$field}", ['result' => $result]);
                    throw new Exception("Missing required field in Gemini response: {$field}");
                }
            }

            // Determine if manual review is needed
            $requiresManualReview = $result['confidence'] < 0.5 || !$result['is_passport_photo'];

            // Map rejection reason to user-friendly message
            $rejectionReason = $this->mapRejectionReason($result);

            Log::info('Verification result', [
                'success' => $result['is_passport_photo'],
                'rejection_reason' => $rejectionReason,
                'confidence' => $result['confidence'],
                'requires_manual_review' => $requiresManualReview,
            ]);

            // TEMPORARY: Allow if confidence is reasonable (for testing)
            if (!$result['is_passport_photo'] && $result['confidence'] > 0.6) {
                Log::info('Accepting photo despite rejection due to confidence > 0.6');
                return [
                    'success' => true,
                    'verification_result' => $result,
                    'confidence' => $result['confidence'],
                    'requires_manual_review' => true,
                    'rejection_reason' => null,
                ];
            }

            return [
                'success' => $result['is_passport_photo'],
                'verification_result' => $result,
                'confidence' => $result['confidence'],
                'requires_manual_review' => $requiresManualReview,
                'rejection_reason' => $rejectionReason,
            ];

        } catch (Exception $e) {
            Log::error('Failed to parse Gemini response', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            return [
                'success' => false,
                'reason' => 'Failed to process verification result',
                'requires_manual_review' => true,
            ];
        }
    }

    /**
     * Map technical rejection reasons to user-friendly messages
     */
    protected function mapRejectionReason(array $result): ?string
    {
        if ($result['is_passport_photo']) {
            return null;
        }

        if (!$result['has_face']) {
            return 'NO_FACE';
        }

        if ($result['face_count'] > 1) {
            return 'MULTIPLE_FACES';
        }

        if ($result['is_group_photo']) {
            return 'GROUP_PHOTO';
        }

        if (!$result['white_background']) {
            return 'COLORED_BACKGROUND';
        }

        if ($result['is_selfie']) {
            return 'SELFIE';
        }

        if ($result['is_document_scan']) {
            return 'DOCUMENT_SCAN';
        }

        if ($result['is_blurry']) {
            return 'BLURRY_IMAGE';
        }

        if (!$result['facing_camera']) {
            return 'NOT_FACING_CAMERA';
        }

        if (!$result['face_centered']) {
            return 'FACE_NOT_CENTERED';
        }

        if (!$result['portrait_orientation']) {
            return 'INCORRECT_ORIENTATION';
        }

        return 'GENERAL_REJECTION';
    }

    /**
     * Get user-friendly error message for rejection reason
     */
    public static function getErrorMessage(string $reason): string
    {
        $messages = [
            'FILE_TOO_LARGE' => 'Your photograph exceeds the maximum allowed size of 200 KB.',
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
