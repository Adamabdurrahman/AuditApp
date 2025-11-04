<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Page') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center items-center h-full min-h-[400px] p-4">
                <!-- Kotak Utama -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 text-center max-w-md w-full transform transition-all hover:scale-[1.02] hover:shadow-2xl duration-300 animate-fade-in-up">

                    <!-- Ikon Gembok SVG Modern -->
                    <div class="flex justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-500 dark:text-indigo-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <!-- Judul -->
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2 tracking-tight">
                        {{ __('Coming Soon') }}
                    </h3>

                    <!-- Pesan -->
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 leading-relaxed">
                        {{ __("This page is currently under development and will be available soon. We're crafting something amazing for you!") }}
                    </p>

                    <!-- Badge Status -->
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 text-indigo-700 dark:text-indigo-300 text-xs px-4 py-1.5 rounded-full font-medium border border-indigo-200 dark:border-indigo-800">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        {{ __('Locked & Building...') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Animasi CSS Custom (Tambahkan di head atau file CSS) -->
    @push('styles')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
    @endpush
</x-app-layout>