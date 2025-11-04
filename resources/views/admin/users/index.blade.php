<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Card utama untuk konten -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                <!-- Header Card: Search dan Tombol Add User -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <!-- Search Input -->
                        <form action="{{ route('admin.users') }}" method="GET">
                            <div class="relative w-full max-w-xs">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="search"  {{-- Tambahkan atribut name --}}
                                    value="{{ request('search') }}" {{-- Tampilkan kembali value pencarian --}}
                                    placeholder="Search users..." 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md ...">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Container Tabel dengan Responsivitas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User Account</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Ganti data dummy dengan data dari controller --}}
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{-- Logika @if Anda untuk warna badge tetap di sini --}}
                                            @if($user->isAdmin()) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($user->isSuperAdmin()) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">

                                            {{-- PERBAIKAN: Ambil properti 'name' dari objek 'role' --}}
                                            {{-- Tanda tanya (?) adalah nullsafe operator, aman jika user tidak punya role --}}
                                            {{ ucfirst($user->role?->name) }}
                                            
                                        </span>
                                    </td>
                                    
                                    <!-- ======================================================= -->
                                    <!-- ACTION DROPDOWN DENGAN SOLUSI FINAL (ALPINE ANCHOR) -->
                                    <!-- ======================================================= -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" x-data="{ open: false }">
                                        <!-- Tombol Trigger -->
                                        <button @click="open = !open" x-ref="anchor" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                        </button>

                                        <!-- Menu Dropdown -->
                                        <div x-show="open"
                                            @click.away="open = false"
                                            x-anchor.bottom-end.offset.8="$refs.anchor"
                                            x-transition
                                            class="w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                                            style="display: none;">
                                            <div class="py-1" role="menu" aria-orientation="vertical">
                                                
                                                <!-- Opsi "Set as Admin" -->
                                                {{-- Hanya tampilkan jika user BUKAN Admin --}}
                                                @if(!$user->isAdmin())
                                                    <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="role_id" value="2"> <!-- GANTI DENGAN ID ROLE ADMIN ANDA -->
                                                        <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            <span>Set as Admin</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Opsi "Set as User" -->
                                                {{-- Hanya tampilkan jika user BUKAN User biasa --}}
                                                @if($user->isAdmin() || $user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="role_id" value="3"> <!-- GANTI DENGAN ID ROLE USER ANDA -->
                                                        <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            <span>Set as User</span>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>