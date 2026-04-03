@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemEmail = \Illuminate\Support\Facades\Cache::get('esa_system_email', 'info@excellentservantacademy.com');
    $systemPhone = \Illuminate\Support\Facades\Cache::get('esa_system_phone', '+1 (555) 123-4567');
    $systemAddress = \Illuminate\Support\Facades\Cache::get('esa_system_address', '123 Education Street, Learning City');
@endphp

<!-- Footer -->
<footer class="bg-gray-800 text-white py-8 mt-16">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Section -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ $systemName }}</h3>
                <p class="text-gray-300 text-sm leading-relaxed">
                    {{ \Illuminate\Support\Facades\Cache::get('esa_system_description', 'Quality education and resources to help students achieve their academic goals.') }}
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-home mr-2"></i>Home</a></li>
                    <li><a href="{{ route('public.exams.index') }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-brain mr-2"></i>Test Skills</a></li>
                    <li><a href="{{ route('public.resources.index') }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-book mr-2"></i>Resources</a></li>
                    <li><a href="{{ url('/fees') }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-credit-card mr-2"></i>Fees</a></li>
                    <li><a href="{{ url('/#quick-access') }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-sign-in-alt mr-2"></i>Portals</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>{{ $systemEmail }}</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        <span>{{ $systemPhone }}</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>{{ $systemAddress }}</span>
                    </li>
                </ul>
            </div>
            
            <!-- Follow Us -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
                <p>Empowering education through quality learning</p>
            </div>
        </div>
    </div>
</footer>
