<div class="fixed top-0 left-0 h-screen w-64 bg-gray-100 p-6 shadow-md flex flex-col">
    <h3 class="font-semibold text-lg text-gray-700 mb-6">Navigation</h3>
    <ul class="space-y-4 flex flex-col flex-grow">
        <li>
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-semibold' : '' }}">
                <!-- Dashboard Icon -->
                <svg class="w-5 h-5 mr-3 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4 0v-8m5-3v12a2 2 0 01-2 2H8a2 2 0 01-2-2V9" />
                </svg>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded hover:bg-gray-200">
                <!-- Profile Icon -->
                <svg class="w-5 h-5 mr-3 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.843.645 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Profile
            </a>
        </li>
        <!-- Add more sidebar links with icons here -->
    </ul>
</div>
