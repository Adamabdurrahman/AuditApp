<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
            </svg>
            Semua Notifikasi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6">

                @forelse($notifications as $notif)
                    @php
                        $color = match($notif->notificationstype_id) {
                            1 => 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300', // Create
                            2 => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300', // Reminder
                            3 => 'border-red-500 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300', // Overdue
                            default => 'border-gray-300 bg-gray-50 dark:bg-gray-900/30 text-gray-300'
                        };
                    @endphp

                    <div class="mb-4 p-4 rounded-lg border-l-4 {{ $color }} hover:shadow-md transition duration-150 ease-in-out">
                        <div class="flex justify-between items-center">
                            <div class="flex items-start gap-2">
                                {{-- Ikon tipe notifikasi --}}
                                @if($notif->notificationstype_id == 1)
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                @elseif($notif->notificationstype_id == 2)
                                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($notif->notificationstype_id == 3)
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.658-1.14 1.086-2.05L13.086 4.95a1.25 1.25 0 00-2.172 0L4.996 16.95c-.572.91.032 2.05 1.086 2.05z" />
                                    </svg>
                                @endif

                                {{-- Isi notifikasi --}}
                                <div>
                                    <p class="font-semibold">{{ $notif->title }}</p>
                                    <p class="text-sm opacity-90">{{ $notif->message }}</p>
                                </div>
                            </div>

                            {{-- Penanda belum dibaca --}}
                            @if(!$notif->is_read)
                                <span class="ml-2 inline-flex items-center justify-center w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-right italic">
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Tidak ada notifikasi untuk ditampilkan.
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
