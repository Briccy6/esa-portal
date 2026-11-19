@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp

<!-- Enhanced Public Navigation -->
