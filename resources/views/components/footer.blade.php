@php
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    $systemName = $schoolInfo?->school_name ?? \Illuminate\Support\Facades\Cache::get('esa_system_name', 'Excellent Servant Academy');
    $systemEmail = $schoolInfo?->email ?? \Illuminate\Support\Facades\Cache::get('esa_system_email', 'info@excellentservantacademy.com');
    $systemPhone = $schoolInfo?->phone ?? \Illuminate\Support\Facades\Cache::get('esa_system_phone', '+250 788 525 247');
    $systemAddress = $schoolInfo?->address ?? \Illuminate\Support\Facades\Cache::get('esa_system_address', 'Rwanda');
    $systemDescription = $schoolInfo?->mission ?? $schoolInfo?->motto ?? \Illuminate\Support\Facades\Cache::get('esa_system_description', 'Learning, opportunity, and a brighter future for every student.');
    $developerMessage = rawurlencode('Hello Nexora Rwanda, I found your developer credit on the ESA website and would like to get in touch.');
@endphp

<footer class="site-footer">
    <div class="site-footer__main">
        <section class="site-footer__about">
            <p class="site-footer__eyebrow">Excellent Servant Academy</p>
            <h2>{{ $systemName }}</h2>
            <p>{{ $systemDescription }}</p>
        </section>

        <nav class="site-footer__links" aria-label="Explore ESA">
            <h3>Explore</h3>
            <a href="{{ route('welcome') }}">Home</a>
            <a href="{{ route('public.skills') }}">Test skills</a>
            <a href="{{ route('public.resources.index') }}">Learning resources</a>
            <a href="{{ route('blogs.public.index') }}">Blog &amp; news</a>
            <a href="{{ route('fees.index') }}">Term fees</a>
            <a href="{{ route('public.babyeyi.index') }}">Babyeyi</a>
        </nav>

        <nav class="site-footer__links" aria-label="Student and portal links">
            <h3>Get started</h3>
            <a href="{{ route('register.choice') }}">Registration</a>
            <a href="{{ route('students.create') }}">Student registration</a>
            <a href="{{ route('public.subscriptions.index') }}">Subscriptions</a>
            <a href="{{ route('welcome') }}#quick-access">Sign in to a portal</a>
        </nav>

        <section class="site-footer__contact">
            <h3>Contact the school</h3>
            <a href="mailto:{{ $systemEmail }}"><i class="fas fa-envelope" aria-hidden="true"></i>{{ $systemEmail }}</a>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $systemPhone) }}"><i class="fas fa-phone" aria-hidden="true"></i>{{ $systemPhone }}</a>
            <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i>{{ $systemAddress }}</p>
        </section>
    </div>

    <div class="site-footer__bottom">
        <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
        <p class="site-footer__credit">
            Designed &amp; developed by
            <a href="https://wa.me/250784571544?text={{ $developerMessage }}" target="_blank" rel="noopener noreferrer">
                Nexora Rwanda <i class="fab fa-whatsapp" aria-hidden="true"></i>
            </a>
        </p>
    </div>
</footer>
