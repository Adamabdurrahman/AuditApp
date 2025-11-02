<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-auto w-20 fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}

                @can('access-user-area')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        {{-- Ini adalah dasbor USER --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('user.my-open-findings')" :active="request()->routeIs('user.my-open-findings')">
                            {{ __('My Open Findings') }}
                        </x-nav-link>
                    </div>
                @endcan

                @can('access-admin-area')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.findings')" :active="request()->routeIs('admin.findings')">
                            {{ __('Findings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.report')" :active="request()->routeIs('admin.report')">
                            {{ __('Report') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                            {{ __('Manage User') }}
                        </x-nav-link>
                    </div>
                @endcan
            
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                
                <!-- Tombol Ikon Notifikasi -->
                <div class="relative me-3" x-data="{ openNotif: false }">
                    <!-- Tombol Lonceng -->
                    <button @click="openNotif = !openNotif" class="relative inline-flex items-center p-2 rounded-md 
                        text-gray-400 dark:text-gray-500 hover:text-green-500 dark:hover:text-green-400 
                        hover:bg-green-100 dark:hover:bg-green-900 focus:outline-none transition duration-150 ease-in-out">
                        <!-- Icon Lonceng -->
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <!-- Badge jumlah notifikasi belum dibaca -->
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center 
                                        px-1.5 py-0.5 text-xs font-bold leading-none text-white 
                                        bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div 
                        x-show="openNotif" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        @click.away="openNotif = false" 
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 shadow-lg rounded-lg border 
                            border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                    >
                        <!-- Header -->
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Notifikasi</span>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="text-xs text-gray-400">({{ $unreadCount }} belum dibaca)</span>
                            @endif
                        </div>

                        <!-- Daftar Notifikasi -->
                        <ul class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($navbarNotifications as $notif)
                                @php
                                    $color = match($notif->notificationstype_id) {
                                        1 => 'text-blue-600',    // Create
                                        2 => 'text-yellow-500',  // Reminder
                                        3 => 'text-red-600',     // Overdue
                                        default => 'text-gray-600'
                                    };
                                @endphp

                                <li 
                                    class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition duration-150 ease-in-out"
                                    data-id="{{ $notif->id }}"
                                    onclick="markNotificationAsRead({{ $notif->id }})"
                                >

                                    <p class="text-sm font-semibold {{ $color }}">
                                        {{ $notif->title }}
                                        @if(!$notif->is_read)
                                            <span class="ml-1 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-snug mt-1">
                                        {{ $notif->message }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </li>
                            @empty
                                <li class="p-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Tidak ada notifikasi
                                </li>
                            @endforelse
                        </ul>

                        <!-- Footer -->
                        <div class="bg-gray-50 dark:bg-gray-900 text-center p-2 text-sm">
                            <a href="{{ route('notifications.index') }}" class="text-green-600 dark:text-green-400 font-semibold hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>


                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    {{-- script untuk nontifikasi --}}
    <script>
        function markNotificationAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus badge merah kecil di item notifikasi
                    document.querySelector(`[data-id="${id}"] span.bg-red-500`)?.remove();

                    // Kurangi counter badge di ikon lonceng
                    const badge = document.querySelector('span.bg-red-600');
                    if (badge) {
                        let count = parseInt(badge.textContent);
                        if (count > 1) badge.textContent = count - 1;
                        else badge.remove();
                    }
                }
            })
            .catch(err => console.error('Error:', err));
        }

        setInterval(() => {
        fetch('/notifications-count')
            .then(res => res.json())
            .then(data => {
                const badge = document.querySelector('span.bg-red-600');
                if (data.count > 0) {
                    if (badge) badge.textContent = data.count;
                    else {
                        // buat badge baru
                        const bell = document.querySelector('button svg');
                        const span = document.createElement('span');
                        span.className = 'absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2';
                        span.textContent = data.count;
                        bell.parentElement.appendChild(span);
                    }
                } else {
                    badge?.remove();
                }
            });
    }, 15000); // update setiap 15 detik
    </script>

</nav>
