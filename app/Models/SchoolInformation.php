<?php

namespace App\Models;

use App\Services\SchoolImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolInformation extends Model
{
    use HasFactory;

    protected $table = 'school_informations';

    protected $fillable = [
        'school_name',
        'address',
        'phone',
        'email',
        'website',
        'logo_path',
        'logo', // BLOB in database (kept for backward compatibility)
        'motto',
        'vision',
        'mission',
        'principal_name',
        'principal_signature',
        'principal_signature_data', // BLOB in database (kept for backward compatibility)
        'stamp_signature',
        'stamp_signature_data', // BLOB in database (kept for backward compatibility)
        'logo_file_path', // NEW: File path to logo in storage
        'principal_signature_file_path', // NEW: File path to principal signature
        'stamp_signature_file_path', // NEW: File path to stamp signature
        'images_migrated_to_storage', // NEW: Flag to track migration status
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'images_migrated_to_storage' => 'boolean',
    ];

    public static function getActive()
    {
        return static::query()
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->first();
    }

    protected function toImageDataUrl(?string $binary, string $fallbackMime = 'image/jpeg'): ?string
    {
        if (empty($binary)) {
            return null;
        }

        $mime = $fallbackMime;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected = $finfo ? finfo_buffer($finfo, $binary) : null;
            if ($finfo) {
                finfo_close($finfo);
            }
            if (is_string($detected) && str_starts_with($detected, 'image/')) {
                $mime = $detected;
            }
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    /**
     * Get logo URL - prioritizes file storage over BLOB
     * Returns file URL if available, falls back to BLOB data URL for backward compatibility
     */
    public function getLogoUrlAttribute(): ?string
    {
        // Prioritize stored file path
        if (!empty($this->attributes['logo_file_path'] ?? null)) {
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($this->attributes['logo_file_path']);
        }

        // Legacy string path (older schema)
        if (!empty($this->attributes['logo_path'] ?? null)) {
            $path = (string) $this->attributes['logo_path'];
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($path);
        }

        // Fallback to BLOB data (deprecated, for backward compatibility)
        $logo = $this->attributes['logo'] ?? null;
        return $logo ? $this->toImageDataUrl($logo) : null;
    }

    /**
     * Get principal signature URL - prioritizes file storage over BLOB
     * Returns file URL if available, falls back to BLOB data URL for backward compatibility
     */
    public function getPrincipalSignatureUrlAttribute(): ?string
    {
        // Prioritize stored file path
        if (!empty($this->attributes['principal_signature_file_path'] ?? null)) {
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($this->attributes['principal_signature_file_path']);
        }

        // Legacy string path (older schema)
        if (!empty($this->attributes['principal_signature'] ?? null)) {
            $path = (string) $this->attributes['principal_signature'];
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($path);
        }

        // Fallback to BLOB data (deprecated, for backward compatibility)
        $signature = $this->attributes['principal_signature_data'] ?? null;
        return $signature ? $this->toImageDataUrl($signature) : null;
    }

    /**
     * Get stamp signature URL - prioritizes file storage over BLOB
     * Returns file URL if available, falls back to BLOB data URL for backward compatibility
     */
    public function getStampSignatureUrlAttribute(): ?string
    {
        // Prioritize stored file path
        if (!empty($this->attributes['stamp_signature_file_path'] ?? null)) {
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($this->attributes['stamp_signature_file_path']);
        }

        // Legacy string path (older schema)
        if (!empty($this->attributes['stamp_signature'] ?? null)) {
            $path = (string) $this->attributes['stamp_signature'];
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $service = app(SchoolImageService::class);
            return $service->getImageUrl($path);
        }

        // Fallback to BLOB data (deprecated, for backward compatibility)
        $signature = $this->attributes['stamp_signature_data'] ?? null;
        return $signature ? $this->toImageDataUrl($signature) : null;
    }
}
