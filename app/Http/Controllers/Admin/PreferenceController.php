<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PreferenceController extends Controller
{
    public function index()
    {
        $prefs = [
            'system_name' => Cache::get('esa_system_name', 'ESA-Parnel'),
            'system_logo' => Cache::get('esa_system_logo', null),
            'youtube_api' => config('esa.youtube_api'),
            'payment_api' => config('esa.payment_api'),
            'google_api' => config('esa.google_api'),
            'recaptcha_api' => config('esa.recaptcha_api'),
            'live_class' => config('esa.live_class'),
        ];
        return view('admin.preferences', compact('prefs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'system_name' => 'nullable|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'youtube_api' => 'nullable|string',
            'payment_api' => 'nullable|string',
            'google_api' => 'nullable|string',
            'recaptcha_api' => 'nullable|string',
            'live_class' => 'nullable|boolean',
        ]);

        if ($request->hasFile('system_logo')) {
            $logoPath = $request->file('system_logo')->store('logos', 'public');
            Cache::forever('esa_system_logo', $logoPath);
            
            // Clear any existing cache to ensure fresh data
            Cache::forget('esa_system_logo');
            Cache::forever('esa_system_logo', $logoPath);
        }

        Cache::forever('esa_system_name', $data['system_name'] ?? 'ESA-Parnel');
        Cache::forever('esa_youtube_api', $data['youtube_api'] ?? '');
        Cache::forever('esa_payment_api', $data['payment_api'] ?? '');
        Cache::forever('esa_google_api', $data['google_api'] ?? '');
        Cache::forever('esa_recaptcha_api', $data['recaptcha_api'] ?? '');
        Cache::forever('esa_live_class', $data['live_class'] ?? 0);

        return back()->with('success', 'Preferences updated!');
    }
}
