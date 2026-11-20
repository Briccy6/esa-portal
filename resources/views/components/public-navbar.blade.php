@php
    $siteName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'Excellent Servant Academy');
    $siteLogo = \App\Helpers\LogoHelper::getLogoUrl();
    $portalLinks = [
        ['route' => 'admin.login.form', 'icon' => 'fa-user-shield', 'label' => 'Administrator'],
        ['route' => 'staff.login', 'icon' => 'fa-users', 'label' => 'Staff'],
        ['route' => 'teacher.login', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Teacher'],
        ['route' => 'student.login', 'icon' => 'fa-user-graduate', 'label' => 'Student'],
        ['route' => 'client.auth.login', 'icon' => 'fa-briefcase', 'label' => 'Client'],
    ];
@endphp

<header class="site-header">
    <div class="site-nav-shell">
        <a class="site-brand" href="{{ route('welcome') }}" aria-label="{{ $siteName }} home">
            @if($siteLogo)
                <img class="site-brand__logo" src="{{ $siteLogo }}" alt="">
            @else
                <span class="site-brand__mark" aria-hidden="true"><i class="fas fa-book-open"></i></span>
            @endif
            <span class="site-brand__copy">
                <span class="site-brand__eyebrow">Excellent Servant Academy</span>
                <span class="site-brand__name">{{ $siteName }}</span>
            </span>
        </a>

        <nav class="site-nav-links" aria-label="Main navigation">
            <a href="{{ route('welcome') }}">Home</a>
            <a href="{{ route('blogs.public.index') }}">Blog</a>
            <a href="{{ route('public.skills') }}">Test skills</a>
            <a href="{{ route('public.resources.index') }}">Learning resources</a>
            <a href="{{ route('fees.index') }}">Fees</a>
            <a href="{{ route('public.babyeyi.index') }}">Babyeyi</a>
            <a href="{{ route('welcome') }}#quick-access">Portals</a>
        </nav>

        <div class="site-nav-actions">
            <details class="site-dropdown">
                <summary class="site-login-trigger">Log in <i class="fas fa-chevron-down" aria-hidden="true"></i></summary>
                <div class="site-dropdown__panel">
                    <p class="site-dropdown__heading">Choose your portal</p>
                    @foreach($portalLinks as $portal)
                        <a href="{{ route($portal['route']) }}">
                            <i class="fas {{ $portal['icon'] }}" aria-hidden="true"></i>
                            <span>{{ $portal['label'] }} portal</span>
                            <i class="fas fa-arrow-right site-dropdown__arrow" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            </details>
            <a class="site-register-link" href="{{ route('register.choice') }}">Register <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>

        <details class="site-mobile-menu">
            <summary aria-label="Open navigation menu"><i class="fas fa-bars" aria-hidden="true"></i><span>Menu</span></summary>
            <div class="site-mobile-menu__panel">
                <nav aria-label="Mobile navigation">
                    <a href="{{ route('welcome') }}">Home</a>
                    <a href="{{ route('blogs.public.index') }}">Blog</a>
                    <a href="{{ route('public.skills') }}">Test skills</a>
                    <a href="{{ route('public.resources.index') }}">Learning resources</a>
                    <a href="{{ route('fees.index') }}">Fees</a>
                    <a href="{{ route('public.babyeyi.index') }}">Babyeyi</a>
                    <a href="{{ route('welcome') }}#quick-access">Portals</a>
                </nav>
                <details class="site-mobile-login">
                    <summary>Log in to a portal <i class="fas fa-chevron-down" aria-hidden="true"></i></summary>
                    <div class="site-mobile-login__links">
                        @foreach($portalLinks as $portal)
                            <a href="{{ route($portal['route']) }}">{{ $portal['label'] }} portal</a>
                        @endforeach
                    </div>
                </details>
                <a class="site-register-link" href="{{ route('register.choice') }}">Register <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </details>
    </div>
</header>
