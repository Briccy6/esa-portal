<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolImageService
{
    private const STORAGE_DISK = 'public';
    private const IMAGE_DIRECTORY = 'school-images';

    /**
     * Store a school image file and return the relative path
     *
     * @param UploadedFile $file
     * @param string $type 'logo', 'principal_signature', or 'stamp_signature'
     * @return string|null The file path relative to storage disk
     */
    public function storeImage(UploadedFile $file, string $type): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        try {
            $filename = $this->generateFilename($file, $type);
            $path = $file->storeAs(
                self::IMAGE_DIRECTORY,
                $filename,
                self::STORAGE_DISK
            );

            return $path;
        } catch (\Exception $e) {
            \Log::error("Failed to store school {$type}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store binary image data from BLOB to file
     *
     * @param string $binaryData
     * @param string $type
     * @param string $extension
     * @return string|null
     */
    public function storeBinaryImage(string $binaryData, string $type, string $extension = 'png'): ?string
    {
        try {
            $filename = $this->generateBinaryFilename($type, $extension);
            $path = self::IMAGE_DIRECTORY . '/' . $filename;
            
            Storage::disk(self::STORAGE_DISK)->put($path, $binaryData);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error("Failed to store binary school {$type}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a school image file
     *
     * @param string|null $filePath
     * @return bool
     */
    public function deleteImage(?string $filePath): bool
    {
        if (empty($filePath)) {
            return true;
        }

        try {
            if (Storage::disk(self::STORAGE_DISK)->exists($filePath)) {
                Storage::disk(self::STORAGE_DISK)->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete school image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the URL for a school image
     *
     * @param string|null $filePath
     * @return string|null
     */
    public function getImageUrl(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        try {
            return Storage::disk(self::STORAGE_DISK)->url($filePath);
        } catch (\Exception $e) {
            \Log::error("Failed to get school image URL: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a filename for an uploaded file
     *
     * @param UploadedFile $file
     * @param string $type
     * @return string
     */
    private function generateFilename(UploadedFile $file, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        return "{$type}_{$timestamp}." . strtolower($extension);
    }

    /**
     * Generate a filename for binary image data
     *
     * @param string $type
     * @param string $extension
     * @return string
     */
    private function generateBinaryFilename(string $type, string $extension): string
    {
        $timestamp = now()->timestamp;
        $uuid = Str::uuid();
        return "{$type}_{$timestamp}_{$uuid}." . strtolower($extension);
    }

    /**
     * Detect image MIME type and get extension
     *
     * @param string $binaryData
     * @return array ['mime' => string, 'extension' => string]
     */
    public function detectImageType(string $binaryData): array
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_buffer($finfo, $binaryData) : 'image/jpeg';
        if ($finfo) {
            finfo_close($finfo);
        }

        $extension = match ($mime) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/jpeg', 'image/jpg' => 'jpg',
            default => 'jpg',
        };

        return [
            'mime' => $mime,
            'extension' => $extension,
        ];
    }

    /**
     * Get old and new file paths for migration
     *
     * @param string $type
     * @param int $schoolId
     * @return string
     */
    public function getLegacyImagePath(string $type, int $schoolId): ?string
    {
        $paths = [
            'logo' => "school-images/logo_school_{$schoolId}.jpg",
            'principal_signature' => "school-images/principal_signature_school_{$schoolId}.jpg",
            'stamp_signature' => "school-images/stamp_signature_school_{$schoolId}.jpg",
        ];

        return $paths[$type] ?? null;
    }
}
