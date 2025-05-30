<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('E-Learning Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-6">

    {{-- 💰 Payment Card --}}
    <div x-data="{ show: false }"
        class="flex-1 min-w-[280px] bg-gradient-to-tr from-green-500 to-green-300 text-black rounded-2xl shadow-md p-6 transition-transform transform hover:scale-105">
        
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-bold">💰 Payment Summary</h3>
            <button @click="show = !show" class="text-sm underline hover:text-green-900 transition">
                <span x-text="show ? 'Hide' : 'Show'"></span>
            </button>
        </div>

        <div class="text-3xl font-bold mb-1">
            <template x-if="show">
                <span>RWF 250,000</span>
            </template>
            <template x-if="!show">
                <span>••••••</span>
            </template>
        </div>

        <p class="text-sm">Payment Date: <span class="font-semibold">2025-05-28</span></p>

        <button class="mt-4 bg-white text-green-700 px-4 py-1 rounded-full text-sm hover:bg-green-100 transition">
            🔄 Reset
        </button>
    </div>

    {{-- 📌 Notice Board --}}
    <div class="flex-1 min-w-[280px] bg-gradient-to-tr from-yellow-400 to-yellow-200 text-yellow-900 rounded-2xl shadow-md p-6 transition-transform transform hover:scale-105">
        <h3 class="text-lg font-bold mb-3">📌 Notice Board</h3>
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li>⚠️ System maintenance on <strong>June 1</strong></li>
            <li>📝 Reports due by <strong>5:00 PM</strong></li>
            <li>🚀 New dashboard launched!</li>
        </ul>
    </div>

    {{-- 📰 News Feed --}}
    <div class="flex-1 min-w-[280px] bg-gradient-to-tr from-blue-400 to-blue-200 text-blue-900 rounded-2xl shadow-md p-6 transition-transform transform hover:scale-105">
        <h3 class="text-lg font-bold mb-3">📰 Latest News</h3>
        <div class="space-y-2 text-sm">
            <p><strong>May 30:</strong> Payment gateway integrated.</p>
            <p><strong>May 28:</strong> UI improved for better experience.</p>
            <p><strong>May 25:</strong> System speed increased by 30%.</p>
        </div>
    </div>

</div>

        </div>
    </div>
</x-app-layout>
