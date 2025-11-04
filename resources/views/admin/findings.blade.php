<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ __('Audit Findings') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Review and Manage audit findings.
                </p>
            </div>
            <!-- Ganti button lama dengan ini -->
            <a href="{{ route('admin.findings.create') }}" 
            class="w-full md:w-auto mt-4 md:mt-0 bg-green-500 text-white font-bold py-2 px-5 rounded-3xl shadow-md hover:bg-green-600 transition duration-300 text-center inline-block">
                + New Finding
            </a>
        </div>
    </x-slot>   

    <div class="py-12 bg-white dark:bg-gray-900">
        
                <form method="GET" action="{{ route('admin.findings') }}">
                    <div class="px-4 sm:px-6 lg:px-8 mb-6">

                        <!-- Satu baris utama: flex-wrap untuk mobile, flex-row untuk desktop -->
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                            <!-- 🔍 FILTER GROUP (semua dropdown & date range) -->
                            <div class="flex flex-wrap items-center gap-3 bg-gray-50 dark:bg-gray-800 
                                px-5 py-3 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 
                                flex-shrink-0">

                                <!-- 🔄 Refresh Button -->
                                <a href="{{ route('admin.findings') }}" 
                                    class="flex items-center justify-center w-10 h-10 rounded-full 
                                        bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 
                                        hover:bg-yellow-200 dark:hover:bg-yellow-800 transition-all duration-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </a>

                                <!-- Status -->
                                <div class="min-w-[120px]">
                                    <select name="status" onchange="this.form.submit()" 
                                        class="w-full px-4 py-2 rounded-xl bg-white dark:bg-gray-700 text-sm border border-gray-300 
                                            dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm">
                                        <option value="">All Status</option>
                                        @foreach ($statuses as $s)
                                            <option value="{{ $s->status }}" {{ request('status') === $s->status ? 'selected' : '' }}>
                                                {{ $s->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div class="min-w-[120px]">
                                    <select name="priority" onchange="this.form.submit()" 
                                        class="w-full px-4 py-2 rounded-xl bg-white dark:bg-gray-700 text-sm border border-gray-300 
                                            dark:border-gray-600 focus:ring-2 focus:ring-green-500 focus:outline-none shadow-sm">
                                        <option value="">All Priority</option>
                                        @foreach ($priorities as $p)
                                            <option value="{{ $p->name }}" {{ request('priority') === $p->name ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Category -->
                                <div class="min-w-[130px]">
                                    <select name="kategori" onchange="this.form.submit()" 
                                        class="w-full px-4 py-2 rounded-xl bg-white dark:bg-gray-700 text-sm border border-gray-300 
                                            dark:border-gray-600 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm">
                                        <option value="">All Category</option>
                                        @foreach ($categories as $c)
                                            <option value="{{ $c->name }}" {{ request('kategori') === $c->name ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Due Date Range -->
                                <div class="flex flex-wrap items-center gap-2 min-w-[240px]">
                                    <input type="date" name="due_start" value="{{ request('due_start') }}"
                                        class="px-4 py-2 rounded-xl bg-white dark:bg-gray-700 text-sm border border-gray-300 
                                            dark:border-gray-600 focus:ring-2 focus:ring-orange-500 focus:outline-none w-full sm:w-[110px]">
                                    <span class="text-gray-500 dark:text-gray-400">–</span>
                                    <input type="date" name="due_end" value="{{ request('due_end') }}" onchange="this.form.submit()"
                                        class="px-4 py-2 rounded-xl bg-white dark:bg-gray-700 text-sm border border-gray-300 
                                            dark:border-gray-600 focus:ring-2 focus:ring-orange-500 focus:outline-none w-full sm:w-[110px]">
                                </div>
                            </div>

                            <!-- 🔎 SEARCH BAR -->
                            <div class="w-full lg:flex-1 lg:min-w-[240px]">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search findings..." 
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-100 dark:bg-gray-700/60 rounded-xl 
                                            focus:outline-none focus:ring-2 focus:ring-green-500 border border-gray-300 dark:border-gray-600 
                                            placeholder:text-gray-500 dark:placeholder:text-gray-400 text-sm shadow-sm"
                                        onkeydown="if(event.key==='Enter'){this.form.submit();}" />
                                </div>
                            </div>

                        </div>
                    </div>
                </form>


                    <br>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fin Loss (Rp)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fin Loss ($)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($findings as $finding)
                                    @php
                                        // Total fin loss
                                        $finLossRp = $finding->kategori->name === 'Fin Loss'
                                            ? $finding->findlossdetails->sum('nilai')
                                            : 0;
                                        $finLossUsd = $finLossRp > 0 ? $finLossRp / $exchangeRate : 0;

                                        // 🎨 Warna status
                                        $statusColor = match($finding->status->status ?? 'Open') {
                                            'Open' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                            'Closed' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                            'Overdue' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300',
                                        };

                                        // 🎨 Warna priority
                                        $priorityColor = match($finding->priority->name ?? '-') {
                                            'High' => 'text-red-600 dark:text-red-400 font-semibold',
                                            'Medium' => 'text-yellow-600 dark:text-yellow-400 font-semibold',
                                            'Low' => 'text-green-600 dark:text-green-400 font-semibold',
                                            default => 'text-gray-500 dark:text-gray-300',
                                        };
                                    @endphp

                                    <tr data-href="{{ route('admin.findings.assessment', $finding->id) }}" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <!-- ID -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                                            {{ $loop->iteration + ($findings->currentPage() - 1) * $findings->perPage() }}
                                        </td>


                                        <!-- Title -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $finding->judul_temuan }}
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $finding->status->status ?? 'Unknown' }}
                                            </span>
                                        </td>

                                        <!-- Priority -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $priorityBadge = match($finding->priority->name ?? '-') {
                                                    'High' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                                    'Medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                                    'Low' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300',
                                                };
                                            @endphp

                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityBadge }}">
                                                {{ $finding->priority->name ?? '-' }}
                                            </span>
                                        </td>


                                        <!-- Kategori -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $finding->kategori->name ?? '-' }}
                                        </td>

                                        <!-- Fin Loss (Rp) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-500">
                                            @if ($finLossRp > 0)
                                                Rp {{ number_format($finLossRp, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Fin Loss ($) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-500">
                                            @if ($finLossUsd > 0)
                                                ${{ number_format($finLossUsd, 2, '.', ',') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Due Date -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($finding->due_date)->format('d M Y') }}
                                        </td>

                                        <!-- Action -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <form action="{{ route('admin.findings.delete', $finding->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this finding?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                    <!-- 🗑️ Heroicon Trash -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No audit findings available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>


                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $findings->links('vendor.pagination.green-compact') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('tbody tr[data-href]');
        rows.forEach(row => {
            row.addEventListener('click', function (e) {
                // Jangan redirect jika klik di dalam tombol/form (misal: delete)
                if (e.target.closest('form') || e.target.closest('button') || e.target.tagName === 'svg' || e.target.tagName === 'path') {
                    return;
                }
                window.location.href = this.dataset.href;
            });
        });
    });
    </script>
    @endpush
</x-app-layout>

