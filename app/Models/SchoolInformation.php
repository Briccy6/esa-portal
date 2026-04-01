<?php

namespace App\Models;

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
        'logo', // BLOB in database (admin uploads store here, not on disk)
        'motto',
        'vision',
        'mission',
        'principal_name',
        'principal_signature',
        'principal_signature_data', // BLOB in database
        'stamp_signature',
        'stamp_signature_data', // BLOB in database
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function getLogoUrlAttribute(): ?string
    {
        $logo = $this->attributes['logo'] ?? null;

        return $logo ? $this->toImageDataUrl($logo) : null;
    }

    public function getPrincipalSignatureUrlAttribute(): ?string
    {
        $signature = $this->attributes['principal_signature_data'] ?? null;

        return $signature ? $this->toImageDataUrl($signature) : null;
    }

    public function getStampSignatureUrlAttribute(): ?string
    {
        $signature = $this->attributes['stamp_signature_data'] ?? null;

        return $signature ? $this->toImageDataUrl($signature) : null;
    }
}
