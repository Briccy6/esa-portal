<?php

namespace App\Support;

class StaffPhoto
{
    public static function url(?string $photo, ?string $fallback = null): ?string
    {
        $photo = trim((string) $photo);

        if ($photo === '') {
            return $fallback;
        }

        if (preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', $photo)) {
            return $photo;
        }

        if (preg_match('/^https?:\/\//i', $photo) || str_starts_with($photo, '/')) {
            return $photo;
        }

        $imageData = base64_decode($photo, true);

        if ($imageData !== false) {
            $imageInfo = @getimagesizefromstring($imageData);

            if ($imageInfo && isset($imageInfo['mime']) && str_starts_with($imageInfo['mime'], 'image/')) {
                return 'data:' . $imageInfo['mime'] . ';base64,' . $photo;
            }

            return $fallback;
        }

        return asset($photo);
    }
}
