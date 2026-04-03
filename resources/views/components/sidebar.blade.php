<aside class="w-64 bg-white shadow-lg h-screen sticky top-0">
    <!-- Sidebar Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                    <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V9a1 1 0 00-1-1h-1v-1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">ESA Portal</h2>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Main Menu</h3>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('dashboard') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4 0v-8m5-3v12a2 2 0 01-2 2H8a2 2 0 01-2-2V9"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.blogs.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.blogs.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V9a1 1 0 00-1-1h-1v-1z"/>
                    </svg>
                    Blog Management
                </a>
            </li>

            <li>
                <a href="{{ route('admin.course.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.course.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Courses
                </a>
            </li>

            <li>
                <a href="{{ route('admin.teachers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.teachers.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Teachers
                </a>
            </li>

            <li>
                <a href="{{ route('admin.staff.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.staff.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Staff
                </a>
            </li>

            <li>
                <a href="{{ route('admin.exams.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.exams.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Exams
                </a>
            </li>

            <li>
                <a href="{{ route('admin.branches.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.branches.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Branches
                </a>
            </li>

            <li>
                <a href="{{ route('admin.students.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('admin.students.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Students
                </a>
            </li>

            <li>
                <form action="{{ route('admin.students.sendBulkInformationEmails') }}" method="POST" onsubmit="return confirm('Are you sure you want to send information emails to ALL students? This may take several minutes.')" class="w-full">
                    @csrf
                    <button type="submit" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 w-full text-left
                           {{ request()->routeIs('admin.students.sendBulkInformationEmails') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Send Info Emails
                    </button>
                </form>
            </li>
        </ul>

        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 mt-8">Other</h3>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('profile.edit') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
            </li>

            <li>
                <a href="{{ route('to-whom.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('to-whom.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Professional Letters
                </a>
            </li>

            <li>
                <a href="{{ route('babyeyi.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200
                   {{ request()->routeIs('babyeyi.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Term Registration
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Announcements
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Messages
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Ask AI
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3 px-4">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">{{ Auth::user()->first_name[0] }}{{ Auth::user()->last_name[0] }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
