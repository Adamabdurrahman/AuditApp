<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('admin.plans.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">Judul Laporan</a>
                    <svg class="mx-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $plan->title }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $plan->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400">Tahun {{ $plan->year }} • Status: <span class="capitalize">{{ $plan->status }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ $plan->findings_count }} Temuan Total
                </span>
                <a href="{{ route('admin.findings.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition">
                    + New Finding
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-xl p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="rounded-lg border border-emerald-100 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-900/20 p-4">
                        <p class="text-xs uppercase text-emerald-700 dark:text-emerald-300">Total Temuan</p>
                        <p class="text-2xl font-semibold text-emerald-800 dark:text-emerald-200 mt-2">{{ $totals['total'] }}</p>
                    </div>
                    <div class="rounded-lg border border-blue-100 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-900/20 p-4">
                        <p class="text-xs uppercase text-blue-700 dark:text-blue-300">Open</p>
                        <p class="text-2xl font-semibold text-blue-800 dark:text-blue-200 mt-2">{{ $totals['open'] }}</p>
                    </div>
                    <div class="rounded-lg border border-green-100 dark:border-green-500/20 bg-green-50 dark:bg-green-900/20 p-4">
                        <p class="text-xs uppercase text-green-700 dark:text-green-300">Closed</p>
                        <p class="text-2xl font-semibold text-green-800 dark:text-green-200 mt-2">{{ $totals['closed'] }}</p>
                    </div>
                    <div class="rounded-lg border border-orange-100 dark:border-orange-500/20 bg-orange-50 dark:bg-orange-900/20 p-4">
                        <p class="text-xs uppercase text-orange-700 dark:text-orange-300">Overdue</p>
                        <p class="text-2xl font-semibold text-orange-800 dark:text-orange-200 mt-2">{{ $totals['overdue'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-xl p-6 space-y-6">
                @php
                    $recoveryChart = $finLossRecoveryChart ?? [
                        'labels' => [],
                        'labels_full' => [],
                        'series' => [
                            'idr' => ['saldo_awal' => [], 'recovery' => [], 'sisa' => []],
                            'usd' => ['saldo_awal' => [], 'recovery' => [], 'sisa' => []],
                        ],
                        'totals' => [
                            'agreed_idr' => 0,
                            'recovery_idr' => 0,
                            'remaining_idr' => 0,
                            'agreed_usd' => 0,
                            'recovery_usd' => 0,
                            'remaining_usd' => 0,
                        ],
                        'meta' => [
                            'finding_events' => 0,
                            'recovery_events' => 0,
                        ],
                        'summaries' => [],
                    ];
                    $hasRecoveryData = count($recoveryChart['labels'] ?? []) > 0;
                    $totalFindingEvents = $recoveryChart['meta']['finding_events'] ?? 0;
                    $totalRecoveryEvents = $recoveryChart['meta']['recovery_events'] ?? 0;
                    $hasSummaries = !empty($recoveryChart['summaries']);
                    $timelineFullLabels = $recoveryChart['labels_full'] ?? [];
                @endphp

                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 space-y-6 bg-gray-50 dark:bg-gray-900/60">
                    <div class="space-y-4">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Timeline Fin Loss Recovery</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Bar hijau = Saldo awal, merah = Recovery, biru = Sisa. Nilai bergerak mengikuti input auditor secara kronologis.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="rounded-xl border border-blue-100 dark:border-blue-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-blue-600 dark:text-blue-300 font-semibold">Total Temuan (IDR)</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($recoveryChart['totals']['agreed_idr'] ?? 0, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Seluruh saldo awal untuk laporan ini.</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-emerald-600 dark:text-emerald-300 font-semibold">Total Recovery (IDR)</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($recoveryChart['totals']['recovery_idr'] ?? 0, 0, ',', '.') }}</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-300 font-semibold">{{ $totalRecoveryEvents }} kali recovery tercatat.</p>
                            </div>
                            <div class="rounded-xl border border-sky-100 dark:border-sky-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-sky-600 dark:text-sky-300 font-semibold">Total Sisa (IDR)</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($recoveryChart['totals']['remaining_idr'] ?? 0, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Saldo outstanding setelah seluruh recovery.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="rounded-xl border border-blue-100 dark:border-blue-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-blue-600 dark:text-blue-300 font-semibold">Total Temuan (USD)</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-50">$ {{ number_format($recoveryChart['totals']['agreed_usd'] ?? 0, 2, '.', ',') }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Konversi saldo awal.</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-emerald-600 dark:text-emerald-300 font-semibold">Total Recovery (USD)</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-50">$ {{ number_format($recoveryChart['totals']['recovery_usd'] ?? 0, 2, '.', ',') }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Nilai pemulihan kumulatif.</p>
                            </div>
                            <div class="rounded-xl border border-sky-100 dark:border-sky-900/40 bg-white dark:bg-gray-900/70 p-4 shadow-sm">
                                <p class="text-[11px] uppercase tracking-wide text-sky-600 dark:text-sky-300 font-semibold">Total Sisa (USD)</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-50">$ {{ number_format($recoveryChart['totals']['remaining_usd'] ?? 0, 2, '.', ',') }}</p>
                                <div class="mt-3 flex items-center justify-between text-[11px] font-medium text-gray-600 dark:text-gray-300">
                                    <span>Temuan: {{ $totalFindingEvents }}</span>
                                    <span>Recovery: {{ $totalRecoveryEvents }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($hasRecoveryData)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <div class="h-72 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">FinLoss Recovery per Judul Temuan (IDR)</h4>
                                <canvas id="plan-finloss-recovery-idr" class="w-full h-full"></canvas>
                            </div>
                            <div class="h-72 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">FinLoss Recovery per Judul Temuan (USD)</h4>
                                <canvas id="plan-finloss-recovery-usd" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    @else
                        <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada item Fin Loss pada filter ini.
                        </div>
                    @endif
                </div>

                @if($hasSummaries)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($recoveryChart['summaries'] as $summary)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900/70 p-5 space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="max-w-[75%]">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100" title="{{ $summary['title'] }}">{{ $summary['title_short'] ?? $summary['title'] }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Event pertama: {{ $summary['first_event_date'] ?? '—' }}</p>
                                    </div>
                                    <a href="{{ route('admin.findings.assessment', $summary['finding_id']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 hover:bg-emerald-200">Detail</a>
                                </div>

                                <div class="grid grid-cols-3 gap-3 text-center">
                                    <div class="rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-3 py-2">
                                        <p class="text-[10px] uppercase tracking-wide text-emerald-600 dark:text-emerald-300 font-semibold">Saldo Awal</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($summary['initial_idr'] ?? 0, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">USD {{ number_format($summary['initial_usd'] ?? 0, 2, '.', ',') }}</p>
                                    </div>
                                    <div class="rounded-md bg-rose-50 dark:bg-rose-900/30 px-3 py-2">
                                        <p class="text-[10px] uppercase tracking-wide text-rose-600 dark:text-rose-300 font-semibold">Recovery</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($summary['recovery_idr'] ?? 0, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">USD {{ number_format($summary['recovery_usd'] ?? 0, 2, '.', ',') }}</p>
                                    </div>
                                    <div class="rounded-md bg-blue-50 dark:bg-blue-900/30 px-3 py-2">
                                        <p class="text-[10px] uppercase tracking-wide text-blue-600 dark:text-blue-300 font-semibold">Sisa</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-50">Rp {{ number_format($summary['remaining_idr'] ?? 0, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">USD {{ number_format($summary['remaining_usd'] ?? 0, 2, '.', ',') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                    <span>Temuan: {{ $summary['finding_events'] }}</span>
                                    <span>Recovery: {{ $summary['recovery_events'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada ringkasan per temuan untuk ditampilkan.
                    </div>
                @endif

                <form method="GET" action="{{ route('admin.plans.show', $plan) }}" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="year" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tahun</label>
                            <select id="year" name="year" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" @selected($selectedYear == $year)>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Status</label>
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->status }}" @selected(($filters['status'] ?? '') === $status->status)>{{ $status->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Prioritas</label>
                            <select name="priority" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="">Semua Prioritas</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->name }}" @selected(($filters['priority'] ?? '') === $priority->name)>{{ $priority->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Kategori</label>
                            <select name="kategori" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" @selected(($filters['kategori'] ?? '') === $category->name)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Cari Temuan</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari judul atau deskripsi temuan..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm pl-10 pr-4 py-2.5 focus:ring-emerald-500 focus:border-emerald-500" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Due Date Dari</label>
                                <input type="date" name="due_start" value="{{ $filters['due_start'] ?? '' }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-orange-500 focus:border-orange-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Due Date Sampai</label>
                                <input type="date" name="due_end" value="{{ $filters['due_end'] ?? '' }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-orange-500 focus:border-orange-500" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow">Terapkan Filter</button>
                        <a href="{{ route('admin.plans.show', ['plan' => $plan->id]) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">Reset</a>
                    </div>
                </form>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/80">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul Temuan</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioritas</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fin Loss (Rp)</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($findings as $finding)
                                        @php
                                            $statusBadge = match($finding->status->status ?? '-') {
                                                'Open' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                                'Closed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                                'Overdue' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-800/40 dark:text-gray-300'
                                            };
                                            $priorityBadge = match($finding->priority->name ?? '-') {
                                                'High' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                                'Medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                'Low' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-800/40 dark:text-gray-300'
                                            };
                                            $finLossTotal = $finding->kategori->name === 'Fin Loss'
                                                ? $finding->findlossdetails->sum('nilai')
                                                : 0;
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 font-medium">
                                                <a href="{{ route('admin.findings.assessment', $finding->id) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">
                                                    {{ $finding->judul_temuan }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                                    {{ $finding->status->status ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $priorityBadge }}">
                                                    {{ $finding->priority->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $finding->kategori->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $finding->due_date ? \Carbon\Carbon::parse($finding->due_date)->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right font-semibold text-red-500">
                                                @if($finLossTotal > 0)
                                                    Rp {{ number_format($finLossTotal, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <form action="{{ route('admin.findings.delete', $finding->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus temuan ini? Tindakan tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-500/90 hover:bg-red-600 text-white text-xs font-semibold shadow-sm transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Belum ada temuan untuk kriteria ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $findings->links('vendor.pagination.green-compact') }}
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Distribusi Temuan per Kategori ({{ $selectedYear }})</h3>
                        <canvas id="plan-category-chart" class="w-full h-64"></canvas>
                        <ul class="mt-4 space-y-2 text-xs text-gray-600 dark:text-gray-400">
                            @foreach($chartData['labels'] as $index => $label)
                                <li class="flex items-center justify-between">
                                    <span>{{ $label }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $chartData['values'][$index] }}</span>
                                </li>
                            @endforeach
                            @if(empty($chartData['labels']))
                                <li class="text-center text-gray-400">Belum ada data kategori.</li>
                            @endif
                        </ul>
                    </div>

                </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const labels = @json($chartData['labels']);
                const dataValues = @json($chartData['values']);

                const ctx = document.getElementById('plan-category-chart');
                if (!ctx) return;

                if (window.planCategoryChart) {
                    window.planCategoryChart.destroy();
                }

                window.planCategoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: [
                                '#34d399', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa',
                                '#f472b6', '#22d3ee', '#fb7185', '#facc15', '#2dd4bf'
                            ],
                            borderWidth: 1,
                            borderColor: '#1f2937'
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--tw-prose-body') || '#374151'
                                }
                            }
                        }
                    }
                });

                const finLossRecoveryData = @json($finLossRecoveryChart ?? null);
                const linkedData = finLossRecoveryData?.linked ?? {};
                const rawLabels = Array.isArray(linkedData.labels) ? linkedData.labels : [];
                const fullLabels = Array.isArray(finLossRecoveryData?.summaries)
                    ? finLossRecoveryData.summaries.map(summary => summary.title ?? summary.title_short ?? 'Temuan')
                    : rawLabels;

                const showRecoveryPlaceholder = (canvasId, message) => {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;
                    const container = canvas.parentElement;
                    canvas.remove();
                    const placeholder = document.createElement('p');
                    placeholder.className = 'h-full flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 text-center';
                    placeholder.textContent = message;
                    container.appendChild(placeholder);
                };

                const createRecoveryChart = (canvasId, labels, currencyData, formatter, detailedLabels) => {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) {
                        return;
                    }

                    if (!labels.length) {
                        showRecoveryPlaceholder(canvasId, 'Belum ada data FinLoss yang dapat ditampilkan.');
                        return;
                    }

                    const events = [];

                    labels.forEach((label, index) => {
                        const friendlyLabel = `${index + 1}. ${label}`;
                        const detailTitle = detailedLabels?.[index] ?? label;

                        const saldoAwal = Number(currencyData?.saldo_awal?.[index] ?? 0);
                        const penambahan = Number(currencyData?.temuan?.[index] ?? 0);
                        const recovery = Number(currencyData?.recovery?.[index] ?? 0);
                        const saldoAkhir = Number(currencyData?.saldo_akhir?.[index] ?? (saldoAwal + penambahan - recovery));

                        const afterAddition = saldoAwal + penambahan;
                        const afterRecovery = Math.max(saldoAkhir, 0);

                        if (penambahan !== 0) {
                            events.push({
                                label: `${friendlyLabel} • Temuan`,
                                range: [saldoAwal, afterAddition],
                                color: '#22c55e',
                                border: '#15803d',
                                type: 'temuan',
                                detail: detailTitle,
                                start: saldoAwal,
                                end: afterAddition
                            });
                        }

                        if (recovery !== 0) {
                            events.push({
                                label: `${friendlyLabel} • Recovery`,
                                range: [afterAddition, afterRecovery],
                                color: 'rgba(239, 68, 68, 0.9)',
                                border: '#b91c1c',
                                type: 'recovery',
                                detail: detailTitle,
                                start: afterAddition,
                                end: afterRecovery
                            });
                        }

                        if (penambahan === 0 && recovery === 0) {
                            events.push({
                                label: `${friendlyLabel} • Saldo Tetap`,
                                range: [saldoAwal, saldoAwal],
                                color: '#9ca3af',
                                border: '#6b7280',
                                type: 'tetap',
                                detail: detailTitle,
                                start: saldoAwal,
                                end: saldoAwal
                            });
                        }
                    });

                    if (!events.length) {
                        showRecoveryPlaceholder(canvasId, 'Belum ada pergerakan saldo FinLoss.');
                        return;
                    }

                    const chartInstance = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: events.map(event => event.label),
                            datasets: [
                                {
                                    label: 'Pergerakan Saldo',
                                    data: events.map(event => event.range),
                                    backgroundColor: ctx => events[ctx.dataIndex].color,
                                    borderColor: ctx => events[ctx.dataIndex].border,
                                    borderWidth: 1.2,
                                    borderSkipped: false,
                                    barPercentage: 0.7,
                                    categoryPercentage: 0.8
                                },
                                {
                                    type: 'line',
                                    label: 'Saldo Setelah Step',
                                    data: events.map(event => event.end),
                                    borderColor: '#0ea5e9',
                                    backgroundColor: '#0ea5e9',
                                    tension: 0,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    showLine: false,
                                    order: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'nearest', intersect: false },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        color: '#4b5563',
                                        font: { family: 'Inter, ui-sans-serif, system-ui', size: 10, weight: '600' },
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 0,
                                        callback: value => {
                                            const raw = events[value]?.label ?? '';
                                            return raw.length > 28 ? `${raw.slice(0, 28)}…` : raw;
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => formatter.format(value ?? 0),
                                        color: '#4b5563',
                                        font: { family: 'Inter, ui-sans-serif, system-ui', size: 11, weight: '600' }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        font: { family: 'Inter, ui-sans-serif, system-ui', size: 11, weight: '600' },
                                        generateLabels: chart => {
                                            const baseLabels = [
                                                { text: 'Temuan (Naik)', fillStyle: '#22c55e', strokeStyle: '#15803d' },
                                                { text: 'Recovery (Turun)', fillStyle: 'rgba(239, 68, 68, 0.9)', strokeStyle: '#b91c1c' },
                                                { text: 'Saldo Setelah Step', fillStyle: '#0ea5e9', strokeStyle: '#0ea5e9', pointStyle: 'circle', lineWidth: 2 }
                                            ];

                                            return baseLabels.map(item => ({
                                                text: item.text,
                                                fillStyle: item.fillStyle,
                                                strokeStyle: item.strokeStyle,
                                                lineWidth: item.lineWidth ?? 1,
                                                pointStyle: item.pointStyle ?? 'rectRounded'
                                            }));
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: context => {
                                            const event = events[context.dataIndex];
                                            if (!event) {
                                                return '';
                                            }

                                            if (context.dataset.type === 'line') {
                                                return `Saldo setelah step: ${formatter.format(event.end)}`;
                                            }

                                            const delta = Math.abs((event.range?.[1] ?? 0) - (event.range?.[0] ?? 0));
                                            const direction = event.type === 'recovery' ? '↓' : event.type === 'temuan' ? '↑' : '•';
                                            return `${direction} ${event.detail}: ${formatter.format(delta)} (Saldo: ${formatter.format(event.end)})`;
                                        },
                                        title: items => items.length ? items[0].label : ''
                                    }
                                }
                            }
                        }
                    });

                    return chartInstance;
                };

                // Fungsi baru untuk Pie Chart yang simple
                const createSimplePieChart = (canvasId, totals, formatter, currency) => {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const totalAwal = totals.agreed || 0;
                    const totalRecovery = totals.recovery || 0;
                    const totalSisa = totals.remaining || 0;

                    if (totalAwal === 0) {
                        showRecoveryPlaceholder(canvasId, 'Belum ada data FinLoss yang dapat ditampilkan.');
                        return;
                    }

                    new Chart(canvas, {
                        type: 'pie',
                        data: {
                            labels: ['Uang Awal', 'Recovery', 'Sisa'],
                            datasets: [{
                                data: [totalAwal, totalRecovery, totalSisa],
                                backgroundColor: [
                                    '#22c55e', // Hijau untuk Uang Awal
                                    '#ef4444', // Merah untuk Recovery
                                    '#3b82f6'  // Biru untuk Sisa
                                ],
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: { size: 12 },
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--tw-prose-body') || '#374151'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return `${label}: ${formatter.format(value)} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                };

                if (finLossRecoveryData && finLossRecoveryData.totals) {
                    // Gunakan Pie Chart yang simple
                    createSimplePieChart(
                        'plan-finloss-recovery-idr',
                        {
                            agreed: finLossRecoveryData.totals.agreed_idr || 0,
                            recovery: finLossRecoveryData.totals.recovery_idr || 0,
                            remaining: finLossRecoveryData.totals.remaining_idr || 0
                        },
                        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }),
                        'IDR'
                    );
                    createSimplePieChart(
                        'plan-finloss-recovery-usd',
                        {
                            agreed: finLossRecoveryData.totals.agreed_usd || 0,
                            recovery: finLossRecoveryData.totals.recovery_usd || 0,
                            remaining: finLossRecoveryData.totals.remaining_usd || 0
                        },
                        new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }),
                        'USD'
                    );
                } else {
                    showRecoveryPlaceholder('plan-finloss-recovery-idr', 'Belum ada data FinLoss yang dapat ditampilkan.');
                    showRecoveryPlaceholder('plan-finloss-recovery-usd', 'Belum ada data FinLoss yang dapat ditampilkan.');
                }
            });
        </script>
    @endpush
</x-app-layout>
