<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountLockingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'setting_key',
        'setting_value',
        'description',
        'is_active',
        'effective_from',
        'effective_until',
        'branch_id',
    ];

    protected $casts = [
        'setting_value' => 'array',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Get the branch that owns the setting
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', now());
            });
    }

    /**
     * Get setting value for a role and key
     */
    public static function getValue($role, $settingKey, $branchId = null, $default = null)
    {
        $setting = self::where('role', $role)
            ->where('setting_key', $settingKey)
            ->where(function($q) use ($branchId) {
                if ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->orWhereNull('branch_id');
                } else {
                    $q->whereNull('branch_id');
                }
            })
            ->active()
            ->orderBy('branch_id', 'desc') // Prefer branch-specific settings
            ->first();

        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set setting value for a role and key
     */
    public static function setValue($role, $settingKey, $value, $branchId = null, $description = null)
    {
        return self::updateOrCreate(
            [
                'role' => $role,
                'setting_key' => $settingKey,
                'branch_id' => $branchId,
            ],
            [
                'setting_value' => is_array($value) ? $value : ['value' => $value],
                'description' => $description,
                'is_active' => true,
                'effective_from' => now(),
            ]
        );
    }
}
