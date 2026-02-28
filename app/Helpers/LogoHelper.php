<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LogoHelper
{
    public static function getLogoUrl()
    {
        // Use the default logo from public/images folder
        return asset('images/esa.jpg');
    }
    
    public static function getLogoPath()
    {
        // Return the default logo path
        return 'images/esa.jpg';
    }
    
    public static function getFaviconUrl()
    {
        // Use the same logo as favicon
        return asset('images/esa.jpg');
    }
    
    public static function hasLogo()
    {
        // Always return true since we're using a static logo
        return true;
    }
    
    public static function getSystemLogo()
    {
        // Get from cache or return default
        return Cache::get('esa_system_logo', 'images/esa.jpg');
    }
} 