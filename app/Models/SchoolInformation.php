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
        'logo', // For database storage
        'motto',
        'vision',
        'mission',
        'principal_name',
        'principal_signature',
        'principal_signature_data', // For database storage
        'stamp_signature',
        'stamp_signature_data', // For database storage
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    public function getLogoUrlAttribute()
    {
        $logo = $this->attributes['logo'] ?? null;
        if ($logo) {
            // Database storage - return base64 data URL
            return 'data:image/jpeg;base64,' . base64_encode($logo);
        } elseif ($this->logo_path) {
            // File storage
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    public function getPrincipalSignatureUrlAttribute()
    {
        $signature = $this->attributes['principal_signature_data'] ?? null;
        if ($signature) {
            // Database storage - return base64 data URL
            return 'data:image/jpeg;base64,' . base64_encode($signature);
        } elseif ($this->principal_signature) {
            // File storage
            return asset('storage/' . $this->principal_signature);
        }
        return null;
    }

    public function getStampSignatureUrlAttribute()
    {
        $signature = $this->attributes['stamp_signature_data'] ?? null;
        if ($signature) {
            // Database storage - return base64 data URL
            return 'data:image/jpeg;base64,' . base64_encode($signature);
        } elseif ($this->stamp_signature) {
            // File storage
            return asset('storage/' . $this->stamp_signature);
        }
        return null;
    }
}
