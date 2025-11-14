<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Audit Plans</h1>
                <p class="text-gray-600 dark:text-gray-400">Daftar rencana audit tahunan sebagai induk temuan.</p>
            </div>
            <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition">
                + New Plan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-xl p-6">
                @if(session('success'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-600/40 dark:bg-emerald-900/40 dark:text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if($plans->isEmpty())
                    <div class="text-center py-12">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum ada audit plan.</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Tambahkan plan terlebih dahulu agar temuan dapat dikaitkan.</p>
                        <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center mt-6 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition">
                            + Buat Plan
                        </a>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach($plans as $year => $items)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tahun {{ $year }}</h2>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $items->count() }} plan</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                    @foreach($items as $plan)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/60 space-y-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $plan->title }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">Status: {{ $statuses[$plan->status] ?? ucfirst($plan->status) }}</p>
                                                </div>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                    {{ $plan->findings_count }} Temuan
                                                </span>
                                            </div>
                                            @if($plan->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ $plan->description }}</p>
                                            @else
                                                <p class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi.</p>
                                            @endif
                                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                                <span>Dibuat: {{ $plan->created_at?->format('d M Y') ?? '-' }}</span>
                                                <a href="{{ route('admin.plans.show', $plan) }}" class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-semibold">
                                                    Lihat Judul Laporan
                                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
