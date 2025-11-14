<x-app-layout>
    <x-slot name="header">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

            <!-- Tombol Kembali -->
            <a href="{{ route('admin.findings') }}" 
            class="px-4 py-2 bg-green-200 hover:bg-green-300 dark:bg-green-700 dark:hover:bg-green-600 text-green-800 dark:text-green-200 rounded-lg font-medium transition">
                ← Back to Findings
            </a>

            <!-- Judul Utama -->
            <div class="md:mt-0 mt-[-4px]"> <!-- Mengurangi jarak atas di mobile -->
                <h1 class="text-center text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ $finding->judul_temuan }}
                </h1>
                <p class="text-center text-gray-600 dark:text-gray-400 mt-1"> <!-- Kurangi margin atas teks deskripsi -->
                    Assessment details for this audit finding.
                </p>
            </div>

            <!-- Tombol Aksi di Kanan -->
            <div class="flex gap-3">
                <a href="{{ strtolower($finding->status->status ?? '') === 'closed' ? '#' : route('admin.findings.confirm', $finding->id) }}"
                class="px-4 py-2 rounded-lg font-medium inline-flex items-center transition
                        {{ strtolower($finding->status->status ?? '') === 'closed'
                            ? 'bg-gray-400 text-gray-200 cursor-not-allowed opacity-60'
                            : 'bg-black text-white hover:bg-gray-900' }}"
                @if(strtolower($finding->status->status ?? '') === 'closed')
                    aria-disabled="true"
                    onclick="return false;"
                @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9-9.5L12 5m0 14l-9-9.5L12 5" />
                    </svg>
                    {{ strtolower($finding->status->status ?? '') === 'closed' ? 'Form Closed' : 'Submit for Review' }}
                </a>
            </div>
        </div>

    </x-slot>

    <div class="py-8 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- =========================================================
                PANEL INFORMASI KHUSUS: Fin Loss → Recovery
            ========================================================= --}}
            @if(
                isset($finding->kategori->name) &&
                strtolower($finding->kategori->name) === 'fin loss' &&
                isset($finding->subkategori->name) &&
                strtolower($finding->subkategori->name) === 'recovery'
            )
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                        <!-- Total Kerugian -->
                        <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 1.343-3 3h6c0-1.657-1.343-3-3-3zm0 0V4m0 8v8m8-8a8 8 0 11-16 0 8 8 0 0116 0z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Kerugian</p>
                                <p class="font-semibold text-red-600 dark:text-red-400">
                                    Rp {{ number_format($totalKerugian ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Total Recovery -->
                        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m-3 5a9 9 0 110-18 9 9 0 010 18z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Recovery</p>
                                <p class="font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($totalRecovery ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Sisa Outstanding -->
                        <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m-3 5a9 9 0 110-18 9 9 0 010 18z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Sisa Outstanding</p>
                                <p class="font-semibold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format($sisaOutputNow ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>


                    </div>
                </div>
            @else
                {{-- =========================================================
                    PANEL DEFAULT (Non-Fin Loss / Recovery)
                ========================================================= --}}
                <!-- Informasi Ringkas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                        <!-- Audit Type -->
                        <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Audit Type</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $finding->kategori->name ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- Lead Auditor -->
                        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 017 7h0a7 7 0 01-7-7z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Lead Auditor</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $finding->auditorUser->name ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="flex items-center gap-3 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Due Date</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $finding->due_date ? \Carbon\Carbon::parse($finding->due_date)->format('d M Y') : '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="flex items-center gap-3 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9M9 9M19 9v6a2 2 0 01-2 2h-2a2 2 0 01-2-2v-6a2 2 0 012-2h2a2 2 0 012 2z" />
                            </svg>
                            <div class="w-full">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Progress</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progressPercent }}%;"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $progressPercent }}%</span>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            @endif

            <!-- Tab Section -->
            <div class="py-8 bg-white dark:bg-gray-900">
                <div class="sm:px-6 lg:px-8">

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="flex space-x-8 overflow-x-auto pb-2">
                            <button type="button" data-tab="info" class="main-tab-button pb-2 px-1 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                Information Detail
                            </button>
                            <button type="button" data-tab="loss" class="main-tab-button pb-2 px-1 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                Fin Loss Detail
                            </button>
                            <button type="button" data-tab="timeline" class="main-tab-button pb-2 px-1 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                Timeline & Auditee 
                            </button>
                            <button type="button" data-tab="attachment" class="main-tab-button pb-2 px-1 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                Attachment
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div id="tab-content" class="space-y-8">

                        <!-- Tab 1: Information Detail -->
                        <div id="info-tab" class="tab-pane block">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                                    <!-- Judul Temuan -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Judul Temuan</label>
                                        <input type="text" 
                                            value="{{ $finding->judul_temuan }}" 
                                            data-field="judul_temuan"
                                            class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Kategori -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Kategori</label>
                                        <select id="kategoriSelect"
                                                data-field="kategori_id" 
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" 
                                                    data-name="{{ $cat->name }}" {{-- ✅ penting untuk logika JS --}}
                                                    {{ $finding->kategori_id == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Sub Kategori -->
                                    <div id="subkategoriWrapper"
                                        style="{{ $finding->kategori->name !== 'Fin Loss' ? 'display:none;' : '' }}">
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Sub Kategori</label>
                                        <select data-field="subkategori_id" 
                                                id="subkategoriSelect"
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500">

                                            {{-- Jika subkategori masih null, tampilkan "Missing" --}}
                                            @if(is_null($finding->subkategori_id))
                                                <option value="" selected disabled>Missing</option>
                                            @endif

                                            {{-- Daftar subkategori dari DB --}}
                                            @foreach($subcategories as $sub)
                                                <option value="{{ $sub->id }}" {{ $finding->subkategori_id == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Priority</label>
                                        <select data-field="priority_id" 
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500">
                                            @foreach($priorities as $pri)
                                                <option value="{{ $pri->id }}" {{ $finding->priority_id == $pri->id ? 'selected' : '' }}>
                                                    {{ $pri->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Temuan Audit -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Temuan Audit (Finding Description)</label>
                                        <textarea rows="4" 
                                                data-field="temuan_audit"
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 resize-none">{{ $finding->temuan_audit }}</textarea>
                                    </div>

                                    <!-- Rekomendasi Author -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Rekomendasi Author</label>
                                        <textarea rows="3"
                                                data-field="rekomendasi_author"
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 resize-none">{{ $finding->rekomendasi_author }}</textarea>
                                    </div>

                                    <!-- Active Respond (Tanggapan Auditee) -->
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Active Respond (Auditee Response)</label>
                                        <textarea rows="3"
                                                data-field="catatan_tambahan"
                                                class="editable-input w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 resize-none">{{ $finding->catatan_tambahan }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Fin Loss Detail -->
                        <div id="loss-tab" class="tab-pane hidden">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Fin Loss Detail</h3>
                                    <button type="button" id="openAddFinLossModal" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                                        </svg>
                                        Tambah Fin Loss
                                    </button>
                                </div>
                                @if($finding->kategori->name === 'Fin Loss')
                                    @php
                                        $lossTotal = $finding->findlossdetails->sum('nilai');
                                        $lossTotalUsd = $finding->findlossdetails->sum(fn($d) => $d->nilai_usd ?? 0);
                                        $paidTotal = $finding->findlossdetails->sum(fn($d) => $d->paid_amount ?? 0);
                                        $paidTotalUsd = $finding->findlossdetails->sum(fn($d) => $d->paid_amount_usd ?? 0);
                                        $remainingTotal = $finding->findlossdetails->sum(fn($d) => max(($d->nilai ?? 0) - ($d->paid_amount ?? 0), 0));
                                        $remainingTotalUsd = $finding->findlossdetails->sum(fn($d) => max(($d->nilai_usd ?? 0) - ($d->paid_amount_usd ?? 0), 0));
                                    @endphp

                                    <div class="overflow-x-auto" id="finLossTableWrapper">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Item</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Nilai Kerugian (Rp)</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Nilai Kerugian (USD)</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total Recovery (Rp)</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total Recovery (USD)</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Sisa (Rp)</th>
                                                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Sisa (USD)</th>
                                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @forelse($finding->findlossdetails as $detail)
                                                    @php
                                                        $paidAmount = $detail->paid_amount ?? 0;
                                                        $paidAmountUsd = $detail->paid_amount_usd ?? 0;
                                                        $remaining = max(($detail->nilai ?? 0) - $paidAmount, 0);
                                                        $remainingUsd = max(($detail->nilai_usd ?? 0) - $paidAmountUsd, 0);
                                                    @endphp
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $detail->item }}</td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-red-600 dark:text-red-400">
                                                            Rp {{ number_format($detail->nilai, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-blue-600 dark:text-blue-300">
                                                            USD {{ number_format((float) ($detail->nilai_usd ?? 0), 2, '.', ',') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-emerald-600 dark:text-emerald-300">
                                                            Rp {{ number_format($paidAmount, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-emerald-600 dark:text-emerald-300">
                                                            USD {{ number_format((float) $paidAmountUsd, 2, '.', ',') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-blue-600 dark:text-blue-300">
                                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-right font-semibold text-blue-600 dark:text-blue-300">
                                                            USD {{ number_format((float) $remainingUsd, 2, '.', ',') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-center space-x-2">
                                                            <button
                                                                type="button"
                                                                class="toggle-recovery inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-500 text-emerald-600 text-xs font-semibold hover:bg-emerald-50 dark:text-emerald-300"
                                                                data-target="recovery-{{ $detail->id }}"
                                                            >
                                                                Riwayat Recovery
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="edit-detail inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-200"
                                                                data-id="{{ $detail->id }}"
                                                                data-item="{{ $detail->item }}"
                                                                data-nilai="{{ $detail->nilai ?? 0 }}"
                                                                data-nilai-usd="{{ $detail->nilai_usd ?? 0 }}"
                                                            >
                                                                Edit
                                                            </button>
                                                            <button class="delete-detail inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-red-100 text-red-600 text-xs font-semibold hover:bg-red-200" data-id="{{ $detail->id }}" title="Hapus item">
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr id="recovery-{{ $detail->id }}" class="hidden bg-gray-50 dark:bg-gray-800/40">
                                                        <td colspan="8" class="px-4 py-4">
                                                            <div class="flex flex-col gap-4">
                                                                <div class="flex flex-col md:flex-row md:items-end gap-3">
                                                                    <div class="flex-1">
                                                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Jumlah Recovery (Rp)</label>
                                                                        <div class="relative">
                                                                            <span class="absolute left-3 top-2 text-xs text-gray-500">Rp</span>
                                                                            <input type="number" step="0.01" min="0.01"
                                                                                class="recovery-amount-input w-full pl-8 pr-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                                                                                placeholder="Masukkan jumlah"
                                                                                data-detail="{{ $detail->id }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Jumlah Recovery (USD)</label>
                                                                        <div class="relative">
                                                                            <span class="absolute left-3 top-2 text-xs text-gray-500">USD</span>
                                                                            <input type="number" step="0.01" min="0"
                                                                                class="recovery-amount-usd-input w-full pl-10 pr-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                                                                                placeholder="Masukkan jumlah"
                                                                                data-detail="{{ $detail->id }}">
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal Recovery</label>
                                                                        <input type="date" class="recovery-date-input w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                                                                            data-detail="{{ $detail->id }}"
                                                                            value="{{ now()->format('Y-m-d') }}">
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Catatan (opsional)</label>
                                                                        <input type="text" class="recovery-notes-input w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                                                                            data-detail="{{ $detail->id }}"
                                                                            placeholder="Contoh: Cicilan ke-1">
                                                                    </div>
                                                                    <button
                                                                        class="add-recovery-btn inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700"
                                                                        data-detail="{{ $detail->id }}"
                                                                    >Tambah Recovery</button>
                                                                </div>

                                                                <div class="overflow-x-auto">
                                                                    <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                                                                        <thead class="bg-gray-100 dark:bg-gray-700/60 text-xs text-gray-600 dark:text-gray-300 uppercase">
                                                                            <tr>
                                                                                <th class="px-3 py-2 text-left">Tanggal</th>
                                                                                <th class="px-3 py-2 text-right">Jumlah (Rp)</th>
                                                                                <th class="px-3 py-2 text-right">Jumlah (USD)</th>
                                                                                <th class="px-3 py-2 text-left">Catatan</th>
                                                                                <th class="px-3 py-2 text-center">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="bg-white dark:bg-gray-900 text-sm">
                                                                            @forelse($detail->recoveries as $recovery)
                                                                                <tr>
                                                                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ optional($recovery->recorded_at)->format('d M Y') ?? '—' }}</td>
                                                                                    <td class="px-3 py-2 text-right text-emerald-600 dark:text-emerald-300">Rp {{ number_format($recovery->amount, 0, ',', '.') }}</td>
                                                                                    <td class="px-3 py-2 text-right text-emerald-600 dark:text-emerald-300">USD {{ number_format((float) ($recovery->amount_usd ?? 0), 2, '.', ',') }}</td>
                                                                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $recovery->notes ?? '—' }}</td>
                                                                                    <td class="px-3 py-2 text-center">
                                                                                        <button class="edit-recovery inline-flex items-center justify-center px-2 py-1 rounded bg-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-200"
                                                                                            data-id="{{ $recovery->id }}"
                                                                                            data-amount="{{ $recovery->amount ?? 0 }}"
                                                                                            data-amount-usd="{{ $recovery->amount_usd ?? 0 }}"
                                                                                            data-recorded-at="{{ optional($recovery->recorded_at)->format('Y-m-d') }}"
                                                                                            data-notes="{{ $recovery->notes ?? '' }}"
                                                                                        >
                                                                                            Edit
                                                                                        </button>
                                                                                        <button class="delete-recovery inline-flex items-center justify-center px-2 py-1 rounded bg-red-100 text-red-600 text-xs font-semibold hover:bg-red-200"
                                                                                            data-id="{{ $recovery->id }}">
                                                                                            Hapus
                                                                                        </button>
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">Belum ada data recovery.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada detail kerugian.</td>
                                                    </tr>
                                                @endforelse

                                                <tr class="font-bold bg-gray-50 dark:bg-gray-700/50">
                                                    <td class="px-4 py-2">Total</td>
                                                    <td class="px-4 py-2 text-right text-red-700 dark:text-red-300">
                                                        Rp {{ number_format($lossTotal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right text-blue-700 dark:text-blue-300">
                                                        USD {{ number_format((float) $lossTotalUsd, 2, '.', ',') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right text-emerald-700 dark:text-emerald-300">
                                                        Rp {{ number_format($paidTotal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right text-emerald-700 dark:text-emerald-300">
                                                        USD {{ number_format((float) $paidTotalUsd, 2, '.', ',') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right text-blue-700 dark:text-blue-300">
                                                        Rp {{ number_format($remainingTotal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right text-blue-700 dark:text-blue-300">
                                                        USD {{ number_format((float) $remainingTotalUsd, 2, '.', ',') }}
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(!empty($detailCharts))
                                        <div class="mt-8 space-y-10">
                                            @foreach($detailCharts as $detailChart)
                                                @php
                                                    $detailId = $detailChart['detail_id'];
                                                    $itemName = $detailChart['item'] ?? ('Detail #' . $detailId);
                                                    $totalsIdr = $detailChart['totals']['idr'] ?? ['agreed' => 0, 'recovery' => 0, 'remaining' => 0];
                                                    $totalsUsd = $detailChart['totals']['usd'] ?? ['agreed' => 0, 'recovery' => 0, 'remaining' => 0];
                                                @endphp

                                                <div class="bg-gray-50 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 rounded-2xl p-6">
                                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
                                                        <div>
                                                            <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200">{{ $itemName }}</h4>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Grafik ini mengikuti saldo berjalan: sisa = sisa sebelumnya + temuan baru − recovery.</p>
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-[220px]">
                                                            <div class="px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50">
                                                                <p class="text-[11px] uppercase tracking-wide text-blue-600 dark:text-blue-300 font-semibold">Total Temuan</p>
                                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 mt-1">Rp {{ number_format($totalsIdr['agreed'] ?? 0, 0, ',', '.') }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">USD {{ number_format($totalsUsd['agreed'] ?? 0, 2, '.', ',') }}</p>
                                                            </div>
                                                            <div class="px-3 py-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40">
                                                                <p class="text-[11px] uppercase tracking-wide text-emerald-600 dark:text-emerald-300 font-semibold">Total Recovery</p>
                                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 mt-1">Rp {{ number_format($totalsIdr['recovery'] ?? 0, 0, ',', '.') }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">USD {{ number_format($totalsUsd['recovery'] ?? 0, 2, '.', ',') }}</p>
                                                            </div>
                                                            <div class="px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40">
                                                                <p class="text-[11px] uppercase tracking-wide text-amber-600 dark:text-amber-300 font-semibold">Sisa Outstanding</p>
                                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 mt-1">Rp {{ number_format($totalsIdr['remaining'] ?? 0, 0, ',', '.') }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">USD {{ number_format($totalsUsd['remaining'] ?? 0, 2, '.', ',') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                                        <div class="h-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pergerakan Temuan vs Recovery (IDR)</h5>
                                                            <canvas id="detail-chart-{{ $detailId }}-idr" data-detail="{{ $detailId }}"></canvas>
                                                        </div>
                                                        <div class="h-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pergerakan Temuan vs Recovery (USD)</h5>
                                                            <canvas id="detail-chart-{{ $detailId }}-usd" data-detail="{{ $detailId }}"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mt-6 text-sm text-gray-500 dark:text-gray-400 text-center">Belum ada detail Fin Loss dengan data numeric untuk divisualisasikan.</p>
                                    @endif

                                    <!-- Modal Edit Fin Loss Detail -->
                                    <div id="addFinLossModal" class="fixed inset-0 z-50 hidden">
                                        <div class="absolute inset-0 bg-gray-900/60" data-close="modal"></div>
                                        <div class="relative mx-auto mt-20 w-full max-w-md px-6">
                                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tambah Fin Loss Detail</h3>
                                                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-close="modal">✕</button>
                                                </div>
                                                <form id="addFinLossForm" class="px-5 py-4 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Item</label>
                                                        <input type="text" id="addFinLossItem" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Kekurangan setoran" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nilai Kerugian (Rp)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">Rp</span>
                                                            <input type="number" min="0" step="0.01" id="addFinLossAmount" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0" required>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nilai Kerugian (USD) <span class="text-gray-400 text-xs font-normal">(opsional)</span></label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">USD</span>
                                                            <input type="number" min="0" step="0.01" id="addFinLossAmountUsd" class="w-full pl-12 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Temuan <span class="text-gray-400 text-xs font-normal">(opsional)</span></label>
                                                        <input type="date" id="addFinLossDate" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500">
                                                    </div>
                                                    <div class="flex justify-end space-x-2 pt-2">
                                                        <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-close="modal">Batal</button>
                                                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="editDetailModal" class="fixed inset-0 z-50 hidden">
                                        <div class="absolute inset-0 bg-gray-900/60" data-close="modal"></div>
                                        <div class="relative mx-auto mt-20 w-full max-w-md px-6">
                                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit Fin Loss Detail</h3>
                                                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-close="modal">✕</button>
                                                </div>
                                                <form id="editDetailForm" class="px-5 py-4 space-y-4">
                                                    <input type="hidden" id="editDetailId">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Item</label>
                                                        <input type="text" id="editDetailItem" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nilai Kerugian (Rp)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">Rp</span>
                                                            <input type="number" min="0" step="0.01" id="editDetailNilai" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nilai Kerugian (USD)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">USD</span>
                                                            <input type="number" min="0" step="0.01" id="editDetailNilaiUsd" class="w-full pl-12 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500">
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end space-x-2 pt-2">
                                                        <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-close="modal">Batal</button>
                                                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit Recovery -->
                                    <div id="editRecoveryModal" class="fixed inset-0 z-50 hidden">
                                        <div class="absolute inset-0 bg-gray-900/60" data-close="modal"></div>
                                        <div class="relative mx-auto mt-24 w-full max-w-md px-6">
                                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit Recovery</h3>
                                                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-close="modal">✕</button>
                                                </div>
                                                <form id="editRecoveryForm" class="px-5 py-4 space-y-4">
                                                    <input type="hidden" id="editRecoveryId">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Jumlah Recovery (Rp)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">Rp</span>
                                                            <input type="number" min="0" step="0.01" id="editRecoveryAmount" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Jumlah Recovery (USD)</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500">USD</span>
                                                            <input type="number" min="0" step="0.01" id="editRecoveryAmountUsd" class="w-full pl-12 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Recovery</label>
                                                        <input type="date" id="editRecoveryDate" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                                                        <input type="text" id="editRecoveryNotes" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Catatan (opsional)">
                                                    </div>
                                                    <div class="flex justify-end space-x-2 pt-2">
                                                        <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-close="modal">Batal</button>
                                                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400">Tidak berlaku — kategori bukan Fin Loss.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tab 3: Timeline & Catatan -->
                        <div id="timeline-tab" class="tab-pane hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                                <!-- 🧍 Auditor & Departemen -->
                                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl shadow-sm border border-blue-100 dark:border-gray-700 p-6">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 007 7H5a7 7 0 007-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Auditor & Departemen</h3>
                                    </div>

                                    <!-- Auditor (read-only) -->
                                    <div class="mb-5">
                                        <label class="block text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide mb-1">Auditor</label>
                                        <div class="px-3 py-2 bg-white dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $finding->auditorUser->name ?? '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Departemen / PIC (editable) -->
                                    <div>
                                        <label class="block text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide mb-1">Departemen / PIC</label>
                                        <input 
                                            type="text"
                                            value="{{ $finding->pic ?? '' }}"
                                            class="editable-input w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            data-field="pic"
                                            placeholder="Masukkan nama departemen"
                                        >
                                    </div>
                                </div>

                                <!-- 🧾 Pihak Auditee -->
                                <div class="bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl shadow-sm border border-green-100 dark:border-gray-700 p-6">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Pihak Auditee</h3>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide mb-1">Perusahaan (PT)</label>
                                            <input 
                                                type="text"
                                                value="{{ $finding->reminder->pt ?? '' }}"
                                                class="editable-input w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                data-field="reminder_pt"
                                                placeholder="Masukkan nama perusahaan"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide mb-1">Nama</label>
                                            <input 
                                                type="text"
                                                value="{{ $finding->reminder->nama ?? '' }}"
                                                class="editable-input w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                data-field="reminder_nama"
                                                placeholder="Masukkan nama auditee"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide mb-1">Email</label>
                                            <input 
                                                type="email"
                                                value="{{ $finding->reminder->email ?? '' }}"
                                                class="editable-input w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                data-field="reminder_email"
                                                placeholder="Masukkan email auditee"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- ⏰ Timeline -->
                                <div class="bg-gradient-to-br from-purple-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl shadow-sm border border-purple-100 dark:border-gray-700 p-6">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Timeline</h3>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Tanggal Temuan -->
                                        <div>
                                            <label class="block text-xs font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-1">Tanggal Temuan</label>
                                            <div class="px-3 py-2 bg-white dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $finding->tanggal_temuan ? \Carbon\Carbon::parse($finding->tanggal_temuan)->format('d M Y') : '—' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Start Date (editable) -->
                                        <div>
                                            <label class="block text-xs font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-1">Start Date</label>
                                            <input 
                                                type="date"
                                                value="{{ $finding->start_date ?? '' }}"
                                                class="editable-input w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                data-field="start_date"
                                            >
                                        </div>

                                        <!-- Due Date -->
                                        <div>
                                            <label class="block text-xs font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-1">Due Date</label>
                                            <div class="px-3 py-2 bg-white dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $finding->due_date ? \Carbon\Carbon::parse($finding->due_date)->format('d M Y') : '—' }}
                                                </p>
                                            </div>
                                        </div>

                                        <button id="extend-btn"
                                            class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow">
                                            Extend
                                        </button>
                                    </div>

                                    <!-- Form Pilih Tanggal Baru (disembunyikan dulu) -->
                                    <div id="extend-form" class="hidden mt-3">
                                        <label class="block text-xs font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-1">
                                            Pilih Due Date Baru
                                        </label>
                                        <input type="date" id="new-due-date"
                                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                            min="{{ $finding->due_date }}" />
                                        <button id="save-extend"
                                            class="mt-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm shadow">
                                            Simpan Perpanjangan
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Tab 4: Attachment -->
                        <div id="attachment-tab" class="tab-pane hidden">
                            <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Lampiran</h3>
                                    </div>

                                    <!-- Tombol Upload -->
                                    <form id="uploadForm" enctype="multipart/form-data" class="flex items-center">
                                        <input type="file" id="uploadFile" name="file" class="hidden" accept=".jpg,.jpeg,.png,.pdf">
                                        <button type="button"
                                            id="uploadBtn"
                                            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 8l-3-3m3 3l3-3" />
                                            </svg>
                                            Upload File
                                        </button>
                                    </form>
                                </div>

                                <!-- Daftar File -->
                                <div id="attachmentList">
                                    @if($finding->fileattachments && $finding->fileattachments->isNotEmpty())
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($finding->fileattachments as $file)
                                                <div class="group bg-white dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 p-4 transition-all hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-800/50" data-file-id="{{ $file->id }}">
                                                    <div class="flex items-start gap-3">
                                                        <!-- Ikon File -->
                                                        <div class="mt-0.5 flex-shrink-0">
                                                            @php
                                                                $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                                            @endphp
                                                            @if($isImage)
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            @endif
                                                        </div>

                                                        <!-- Info File -->
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                                {{ $file->file_name }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                {{ round($file->file_size / 1024, 1) }} KB
                                                            </p>
                                                        </div>

                                                        <!-- Tombol Aksi (muncul saat hover) -->
                                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <!-- Lihat -->
                                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                                class="p-1.5 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                                                                title="Lihat File">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 10l4.553 4.553a2 2 0 01-2.828 2.828L12 12.828l-4.725 4.553a2 2 0 11-2.828-2.828L9 10" />
                                                                </svg>
                                                            </a>
                                                            <!-- Hapus -->
                                                            <button class="delete-file p-1.5 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30"
                                                                data-id="{{ $file->id }}" title="Hapus File">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-10">
                                            <div class="inline-block p-3 bg-gray-100 dark:bg-gray-700/50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada lampiran</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Upload file untuk menambahkan dokumen pendukung.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                // const tabButtons = document.querySelectorAll('[data-tab]');
                const mainTabButtons = document.querySelectorAll('.main-tab-button');
                // const tabPanes = document.querySelectorAll('.tab-pane');
                const mainTabPanes = document.querySelectorAll('.tab-pane');
                const editableInputs = document.querySelectorAll('.editable-input');
                const findingId = {{ $finding->id }};
                const csrfToken = '{{ csrf_token() }}';

                // Fungsi aktivasi tab
                function activateMainTab(tabName) {
                    mainTabButtons.forEach(btn => {
                        btn.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-blue-500');
                        btn.classList.add('text-gray-500', 'dark:text-gray-400');
                    });
                    mainTabPanes.forEach(pane => pane.classList.add('hidden'));
                    const btn = document.querySelector(`.main-tab-button[data-tab="${tabName}"]`);
                    const pane = document.getElementById(`${tabName}-tab`);
                    if (btn) {
                        btn.classList.remove('text-gray-500', 'dark:text-gray-400');
                        btn.classList.add('text-blue-600', 'dark:text-blue-400', 'border-blue-500');
                    }
                    if (pane) {
                        pane.classList.remove('hidden');
                    }
                }

                activateMainTab('info');

                mainTabButtons.forEach(btn => {
                    btn.addEventListener('click', () => activateMainTab(btn.dataset.tab));
                });

                // Auto-save logic
                editableInputs.forEach(input => {
                    input.addEventListener('change', async function() {
                        const field = this.dataset.field;
                        const value = this.value;

                        try {
                            const response = await fetch(`/admin/findings/${findingId}/autosave`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ field, value })
                            });

                            const result = await response.json();
                            if (result.success) {
                                this.classList.add('border-green-500');
                                setTimeout(() => this.classList.remove('border-green-500'), 1000);
                            } else {
                                this.classList.add('border-red-500');
                                console.error(result.error || 'Save failed');
                            }
                        } catch (error) {
                            console.error('Error saving field', field, error);
                            this.classList.add('border-red-500');
                        }
                    });
                });

                const kategoriSelect = document.getElementById('kategoriSelect');
                const subkategoriWrapper = document.getElementById('subkategoriWrapper');
                const subkategoriSelect = document.getElementById('subkategoriSelect');

                if (kategoriSelect) {
                    kategoriSelect.addEventListener('change', function() {
                        const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
                        const selectedName = selectedOption.dataset.name;

                        if (selectedName === 'Fin Loss') {
                            // ✅ Jika kategori Fin Loss → tampilkan dropdown
                            subkategoriWrapper.style.display = 'block';
                        } else {
                            // 🚫 Jika bukan Fin Loss → sembunyikan dan kosongkan nilai
                            subkategoriWrapper.style.display = 'none';
                            

                            // Kosongkan value agar tidak tersimpan data lama
                            if (subkategoriSelect) {
                                subkategoriSelect.value = '';

                                // 🔄 Jika kamu ingin auto-save ke DB agar value null
                                // ketika Fin Loss diganti ke kategori lain, bisa tambahkan ini:
                                const findingId = {{ $finding->id }};
                                const csrfToken = '{{ csrf_token() }}';
                                fetch(`/admin/findings/${findingId}/autosave`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({
                                        field: 'subkategori_id',
                                        value: ''
                                    })
                                }).then(res => res.json())
                                .then(data => console.log('Subkategori cleared:', data));
                            }
                        }
                    });
                }

                // Delegasi event: klik tombol delete
                const editModal = document.getElementById('editDetailModal');
                const editForm = document.getElementById('editDetailForm');
                const editIdInput = document.getElementById('editDetailId');
                const editItemInput = document.getElementById('editDetailItem');
                const editNilaiInput = document.getElementById('editDetailNilai');
                const editNilaiUsdInput = document.getElementById('editDetailNilaiUsd');

                const editRecoveryModal = document.getElementById('editRecoveryModal');
                const editRecoveryForm = document.getElementById('editRecoveryForm');
                const editRecoveryIdInput = document.getElementById('editRecoveryId');
                const editRecoveryAmountInput = document.getElementById('editRecoveryAmount');
                const editRecoveryAmountUsdInput = document.getElementById('editRecoveryAmountUsd');
                const editRecoveryDateInput = document.getElementById('editRecoveryDate');
                const editRecoveryNotesInput = document.getElementById('editRecoveryNotes');

                const toggleModal = (modal, show) => {
                    if (!modal) return;
                    modal.classList.toggle('hidden', !show);
                    document.body.classList.toggle('overflow-hidden', show);
                };

                const addFinLossModal = document.getElementById('addFinLossModal');
                const addFinLossForm = document.getElementById('addFinLossForm');
                const addFinLossItemInput = document.getElementById('addFinLossItem');
                const addFinLossAmountInput = document.getElementById('addFinLossAmount');
                const addFinLossAmountUsdInput = document.getElementById('addFinLossAmountUsd');
                const addFinLossDateInput = document.getElementById('addFinLossDate');
                const openAddFinLossModalBtn = document.getElementById('openAddFinLossModal');

                const openEditModal = (detail) => {
                    editIdInput.value = detail.id;
                    editItemInput.value = detail.item;
                    editNilaiInput.value = detail.nilai;
                    editNilaiUsdInput.value = detail.nilaiUsd;
                    toggleModal(editModal, true);
                };

                const closeEditModal = () => toggleModal(editModal, false);
                const closeAddFinLossModal = () => toggleModal(addFinLossModal, false);

                const openRecoveryModal = (recovery) => {
                    editRecoveryIdInput.value = recovery.id;
                    editRecoveryAmountInput.value = recovery.amount;
                    editRecoveryAmountUsdInput.value = recovery.amountUsd;
                    editRecoveryDateInput.value = recovery.recordedAt || '';
                    editRecoveryNotesInput.value = recovery.notes || '';
                    toggleModal(editRecoveryModal, true);
                };

                const closeRecoveryModal = () => toggleModal(editRecoveryModal, false);

                document.querySelectorAll('#editDetailModal [data-close="modal"]').forEach(el => {
                    el.addEventListener('click', closeEditModal);
                });
                document.querySelectorAll('#addFinLossModal [data-close="modal"]').forEach(el => {
                    el.addEventListener('click', closeAddFinLossModal);
                });
                document.querySelectorAll('#editRecoveryModal [data-close="modal"]').forEach(el => {
                    el.addEventListener('click', closeRecoveryModal);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeEditModal();
                        closeAddFinLossModal();
                        closeRecoveryModal();
                    }
                });

                if (openAddFinLossModalBtn) {
                    openAddFinLossModalBtn.addEventListener('click', () => {
                        addFinLossForm?.reset();
                        toggleModal(addFinLossModal, true);
                        setTimeout(() => addFinLossItemInput?.focus(), 100);
                    });
                }

                if (addFinLossForm) {
                    addFinLossForm.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const item = addFinLossItemInput.value.trim();
                        const nilai = parseFloat(addFinLossAmountInput.value || '0');
                        const nilaiUsd = parseFloat(addFinLossAmountUsdInput.value || '0');
                        const recordedAt = addFinLossDateInput.value || null;

                        if (!item || nilai <= 0) {
                            return alert('Isi nama item dan nilai kerugian (Rp) minimal lebih dari 0.');
                        }

                        const payload = {
                            item,
                            nilai,
                            nilai_usd: isNaN(nilaiUsd) ? 0 : nilaiUsd,
                            recorded_at: recordedAt,
                        };

                        const submitBtn = addFinLossForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Menyimpan...';
                        submitBtn.disabled = true;

                        try {
                            const res = await fetch(`/admin/findlossdetail/${findingId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!res.ok) {
                                const data = await res.json().catch(() => null);
                                throw new Error(data?.error || 'Gagal menambah detail.');
                            }

                            closeAddFinLossModal();
                            location.reload();
                        } catch (error) {
                            alert(error.message || 'Terjadi kesalahan saat menyimpan detail.');
                        } finally {
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        }
                    });
                }

                document.querySelectorAll('.edit-detail').forEach(btn => {
                    btn.addEventListener('click', () => {
                        openEditModal({
                            id: btn.dataset.id,
                            item: btn.dataset.item || '',
                            nilai: btn.dataset.nilai || 0,
                            nilaiUsd: btn.dataset.nilaiUsd || 0,
                        });
                    });
                });

                document.querySelectorAll('.edit-recovery').forEach(btn => {
                    btn.addEventListener('click', () => {
                        openRecoveryModal({
                            id: btn.dataset.id,
                            amount: btn.dataset.amount || 0,
                            amountUsd: btn.dataset.amountUsd || 0,
                            recordedAt: btn.dataset.recordedAt || '',
                            notes: btn.dataset.notes || '',
                        });
                    });
                });

                if (editForm) {
                    editForm.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        const id = editIdInput.value;
                        if (!id) return;

                        const payload = {
                            item: editItemInput.value.trim(),
                            nilai: parseFloat(editNilaiInput.value || '0'),
                            nilai_usd: parseFloat(editNilaiUsdInput.value || '0'),
                        };

                        const submitBtn = editForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Menyimpan...';
                        submitBtn.disabled = true;

                        try {
                            const res = await fetch(`/admin/findlossdetail/${id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!res.ok) {
                                const data = await res.json().catch(() => null);
                                throw new Error(data?.error || 'Gagal mengupdate detail.');
                            }

                            closeEditModal();
                            location.reload();
                        } catch (error) {
                            alert(error.message || 'Terjadi kesalahan saat menyimpan perubahan.');
                        } finally {
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        }
                    });
                }

                if (editRecoveryForm) {
                    editRecoveryForm.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        const id = editRecoveryIdInput.value;
                        if (!id) return;

                        const payload = {
                            amount: parseFloat(editRecoveryAmountInput.value || '0'),
                            amount_usd: parseFloat(editRecoveryAmountUsdInput.value || '0'),
                            recorded_at: editRecoveryDateInput.value || null,
                            notes: editRecoveryNotesInput.value || null,
                        };

                        const submitBtn = editRecoveryForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Menyimpan...';
                        submitBtn.disabled = true;

                        try {
                            const res = await fetch(`/admin/findlossdetail/recovery/${id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!res.ok) {
                                const data = await res.json().catch(() => null);
                                throw new Error(data?.error || 'Gagal mengupdate recovery.');
                            }

                            closeRecoveryModal();
                            location.reload();
                        } catch (error) {
                            alert(error.message || 'Terjadi kesalahan saat menyimpan recovery.');
                        } finally {
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        }
                    });
                }

                document.querySelectorAll('.delete-detail').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const id = this.dataset.id;

                        if (!confirm('Apakah Anda yakin ingin menghapus detail ini?')) return;

                        try {
                            const res = await fetch(`/admin/findlossdetail/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken },
                            });
                            const data = await res.json();

                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Gagal menghapus: ' + (data.error || 'Terjadi kesalahan'));
                            }
                        } catch (err) {
                            alert('Error saat menghapus data.');
                            console.error(err);
                        }
                    });
                });

                document.querySelectorAll('.toggle-recovery').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const targetId = btn.dataset.target;
                        const row = document.getElementById(targetId);
                        if (!row) return;

                        row.classList.toggle('hidden');
                        if (!row.classList.contains('hidden')) {
                            btn.classList.add('bg-emerald-500', 'text-white');
                        } else {
                            btn.classList.remove('bg-emerald-500', 'text-white');
                        }
                    });
                });

                document.querySelectorAll('.add-recovery-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const detailId = btn.dataset.detail;
                        const amountInput = document.querySelector(`.recovery-amount-input[data-detail="${detailId}"]`);
                        const dateInput = document.querySelector(`.recovery-date-input[data-detail="${detailId}"]`);
                        const notesInput = document.querySelector(`.recovery-notes-input[data-detail="${detailId}"]`);

                        if (!amountInput) return;

                        const amount = parseFloat(amountInput.value || '0');
                        if (!amount || amount <= 0) {
                            alert('Masukkan jumlah recovery yang valid.');
                            return;
                        }

                        btn.disabled = true;
                        const originalText = btn.textContent;
                        btn.textContent = 'Menyimpan...';

                        try {
                            const res = await fetch(`/admin/findlossdetail/${detailId}/recovery`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    amount,
                                    recorded_at: dateInput ? dateInput.value : null,
                                    notes: notesInput ? notesInput.value : null,
                                })
                            });

                            if (!res.ok) {
                                const data = await res.json();
                                throw new Error(data.error || 'Gagal menambah recovery');
                            }

                            location.reload();
                        } catch (err) {
                            alert(err.message || 'Terjadi kesalahan saat menyimpan.');
                            btn.disabled = false;
                            btn.textContent = originalText;
                        }
                    });
                });

                document.querySelectorAll('.delete-recovery').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        if (!confirm('Hapus data recovery ini?')) return;

                        const id = btn.dataset.id;

                        try {
                            const res = await fetch(`/admin/findlossdetail/recovery/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            if (!res.ok) {
                                const data = await res.json();
                                throw new Error(data.error || 'Gagal menghapus recovery');
                            }

                            location.reload();
                        } catch (err) {
                            alert(err.message || 'Terjadi kesalahan saat menghapus.');
                        }
                    });
                });

                // ✅ Upload Attachment
                const uploadBtn = document.getElementById('uploadBtn');
                const uploadFile = document.getElementById('uploadFile');

                
                if (uploadBtn && uploadFile) {
                    uploadBtn.addEventListener('click', () => uploadFile.click());

                    uploadFile.addEventListener('change', async () => {
                        if (uploadFile.files.length === 0) return;

                        const file = uploadFile.files[0];
                        const formData = new FormData();
                        formData.append('file', file);

                        // Ubah tombol jadi status "menyimpan..."
                        uploadBtn.disabled = true;
                        uploadBtn.innerText = 'Menyimpan...';

                        try {
                            const res = await fetch(`/admin/findings/${findingId}/attachments`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrfToken },
                                body: formData
                            });

                            const data = await res.json();

                            if (data.success) {
                                // ✅ Upload sukses
                                uploadBtn.innerText = 'Berhasil! 🔄';
                                setTimeout(() => location.reload(), 800); // auto refresh agar list terupdate
                            } else {
                                if (data.error?.includes('5 MB')) {
                                    alert('Total file sudah mencapai batas maksimum 5 MB. Hapus file lain sebelum upload baru.');
                                } else {
                                    alert('Gagal upload: ' + (data.error || 'Terjadi kesalahan.'));
                                }

                                uploadBtn.innerText = 'Upload File';
                                uploadBtn.disabled = false;
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Error saat upload file.');
                            uploadBtn.innerText = 'Upload File';
                            uploadBtn.disabled = false;
                        }
                    });
                }


                // ✅ Hapus Attachment
                document.querySelectorAll('.delete-file').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const id = this.dataset.id;
                        if (!confirm('Hapus file ini?')) return;

                        try {
                            const res = await fetch(`/admin/attachments/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken }
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.closest('[data-file-id]').remove();
                            } else {
                                alert('Gagal menghapus file.');
                            }
                        } catch (err) {
                            alert('Error saat menghapus file.');
                        }
                    });
                });

                document.getElementById('extend-btn').addEventListener('click', () => {
                    document.getElementById('extend-form').classList.toggle('hidden');
                });

                document.getElementById('save-extend').addEventListener('click', async () => {
                    const newDate = document.getElementById('new-due-date').value;
                    if (!newDate) return alert('Pilih tanggal baru terlebih dahulu!');

                    const res = await fetch(`/admin/findings/{{ $finding->id }}/extend`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ due_date: newDate })
                    });
                    const data = await res.json();

                    if (data.success) {
                        alert('✅ Due date berhasil diperpanjang!');
                        location.reload();
                    } else {
                        alert('❌ ' + data.error);
                    }
                });
            });
            </script>
            @endpush



            <!-- =========================
                    ⚙️ SCRIPT
                ========================== -->
            <script>
            const subTabButtons = document.querySelectorAll('.sub-tab-btn');

            subTabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.dataset.tab;
                    subTabButtons.forEach(b =>
                        b.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400')
                    );
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    document.querySelectorAll('#detailPanel [id^="tab-"]').forEach(c => c.classList.add('hidden'));
                    document.getElementById(`tab-${tab}`).classList.remove('hidden');
                });
            });

            const openAddModal = document.getElementById('openAddModal');
            const addModal = document.getElementById('addModal');
            const cancelAdd = document.getElementById('cancelAdd');
            const saveAdd = document.getElementById('saveAdd');
            const list = document.getElementById('assessmentList');
            const count = document.getElementById('item-count');
            const detailPanel = document.getElementById('detailPanel');

            const titleInput = document.getElementById('detailTitleInput');
            const descInput = document.getElementById('detailDescriptionInput');
            const typeInput = document.getElementById('detailTypeInput');
            const testDate = document.getElementById('detailTestDate');
            const testPerformed = document.getElementById('detailTestingPerformed');

            let currentAssessmentId = null;

            // ===================== MODAL =====================
            openAddModal.addEventListener('click', () => addModal.classList.remove('hidden'));
            cancelAdd.addEventListener('click', () => addModal.classList.add('hidden'));

            // ===================== LOAD DETAIL =====================
            async function loadAssessmentDetail(id) {
                const res = await fetch(`/admin/assessments/${id}`);
                const data = await res.json().catch(() => null);
                if (!data || !data.success) return;

                const a = data.assessment;
                currentAssessmentId = a.id;
                window.currentAssessmentId = a.id; 

                titleInput.value = a.title ?? '';
                descInput.value = a.description ?? '';

                // Normalisasi nilai dropdown (case-insensitive)
                typeInput.value = a.type ?? '';

                testDate.value = a.test_date ?? '';
                testPerformed.value = a.testing_performed ?? '';

                detailPanel.classList.remove('opacity-50', 'pointer-events-none');

                if (typeof loadRecoveries === 'function') {
                    loadRecoveries(currentAssessmentId);
                }
            }

            // ===================== AUTO SAVE =====================
            async function autoSave(field, value) {
                if (!currentAssessmentId) return;
                try {
                    await fetch(`/admin/assessments/${currentAssessmentId}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ field, value }),
                    });
                } catch (e) {
                    console.error('Autosave failed:', e);
                }
            }

            // Event listener untuk auto-save
            titleInput.addEventListener('input', e => autoSave('title', e.target.value));
            descInput.addEventListener('input', e => autoSave('description', e.target.value));
            typeInput.addEventListener('input', e => {
                const val = e.target.value.trim();
                const formatted = val.charAt(0).toUpperCase() + val.slice(1);
                autoSave('type', formatted);
            });
            testDate.addEventListener('change', e => autoSave('test_date', e.target.value));
            testPerformed.addEventListener('input', e => autoSave('testing_performed', e.target.value));

            // ===================== CLICK ITEM =====================
            function attachAssessmentClickEvents() {
                document.querySelectorAll('#assessmentList > div').forEach(box => {
                    box.addEventListener('click', () => {
                        document.querySelectorAll('#assessmentList > div').forEach(b => b.classList.remove('ring', 'ring-purple-500'));
                        box.classList.add('ring', 'ring-purple-500');
                        const id = box.querySelector('button.delete-assessment').dataset.id;
                        loadAssessmentDetail(id);
                    });
                });
            }
            attachAssessmentClickEvents();

            // ===================== DELETE ASSESSMENT =====================
            document.addEventListener('click', async (e) => {
                if (!e.target.closest('.delete-assessment')) return;
                const btn = e.target.closest('.delete-assessment');
                const id = btn.dataset.id;

                if (!confirm('Yakin ingin menghapus assessment ini beserta semua Recovery terkait?')) return;

                try {
                    const res = await fetch(`/admin/assessments/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });

                    const data = await res.json().catch(() => null);
                    if (!data?.success) {
                        alert('Gagal menghapus assessment.');
                        return;
                    }

                    // Hapus elemen dari tampilan
                    btn.closest('.group').remove();

                    // Reset detail panel
                    document.getElementById('detailPanel').classList.add('opacity-50', 'pointer-events-none');
                    document.getElementById('recoveryTableBody').innerHTML =
                        `<tr><td colspan="4" class="text-center py-3 text-gray-500">
                            Pilih Assessment untuk melihat Recovery
                        </td></tr>`;

                    // Reset total recovery
                    document.getElementById('totalRp').textContent = 'Rp 0';
                    document.getElementById('totalUsd').textContent = 'USD 0.00';
                } catch (err) {
                    console.error('Delete failed:', err);
                }
            });


            // ===================== AUTO SELECT PERTAMA =====================
            window.addEventListener('DOMContentLoaded', () => {
                const firstBox = document.querySelector('#assessmentList > div');
                if (firstBox) {
                    firstBox.classList.add('ring', 'ring-purple-500');
                    const id = firstBox.querySelector('button.delete-assessment').dataset.id;
                    loadAssessmentDetail(id);
                }
            });

            // ===================== ADD ITEM =====================
            saveAdd.addEventListener('click', async () => {
                const title = document.getElementById('assessmentTitle').value.trim();
                const description = document.getElementById('assessmentDescription').value.trim();
                if (!title) return alert('Title required');

                const res = await fetch(`/admin/assessments/{{ $finding->id }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ title, description })
                });

                const data = await res.json().catch(() => null);
                if (!data || !data.success) return alert('Failed to add item.');

                const div = document.createElement('div');
                div.className = 'p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 flex justify-between items-start cursor-pointer';
                div.innerHTML = `
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">${data.assessment.title}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">${data.assessment.description ?? ''}</p>
                    </div>
                    <button class="delete-assessment text-red-500 hover:text-red-700" data-id="${data.assessment.id}">🗑️</button>
                `;
                div.classList.add('transition', 'transform', 'duration-200', 'scale-95');
                list.prepend(div);
                count.textContent = parseInt(count.textContent) + 1;

                document.getElementById('assessmentTitle').value = '';
                document.getElementById('assessmentDescription').value = '';
                addModal.classList.add('hidden');

                attachAssessmentClickEvents();
                div.click();
            });

            // ===================== TAB SWITCH =====================
            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.dataset.tab;
                    tabButtons.forEach(b => b.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400'));
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    document.querySelectorAll('#detailPanel [id^="tab-"]').forEach(c => c.classList.add('hidden'));
                    document.getElementById(`tab-${tab}`).classList.remove('hidden');
                });
            });
            </script>


            {{-- khusus untuk recovery --}}
            <script>
            /* =======================================================
            SCRIPT RECOVERY - FIXED VERSION (langsung bisa jalan)
            ======================================================= */
            const recoveryName = document.getElementById('recoveryName');
            const recoveryValue = document.getElementById('recoveryValue');
            const addRecoveryBtn = document.getElementById('addRecoveryBtn');
            const recoveryTableBody = document.getElementById('recoveryTableBody');
            const totalRp = document.getElementById('totalRp');
            const totalUsd = document.getElementById('totalUsd');

            let totalRpValue = 0;
            let totalUsdValue = 0;

            function formatRp(value) {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            }

            function formatUsd(value) {
                return 'USD ' + Number(value).toFixed(2);
            }

            /* ===================== LOAD RECOVERY ===================== */
            async function loadRecoveries(assessmentId) {
                if (!assessmentId) {
                    recoveryTableBody.innerHTML = `
                        <tr><td colspan="4" class="text-center py-3 text-gray-500">
                            Pilih Assessment untuk melihat Recovery
                        </td></tr>`;
                    totalRp.textContent = 'Rp 0';
                    totalUsd.textContent = 'USD 0.00';
                    return;
                }

                console.log('📦 Memuat Recovery untuk Assessment ID:', assessmentId);

                const res = await fetch(`/admin/recovery/${assessmentId}`);
                const data = await res.json().catch(() => null);

                if (!data?.success) {
                    console.warn('❌ Gagal memuat recovery');
                    return;
                }

                recoveryTableBody.innerHTML = ''; 
                totalRpValue = 0;
                totalUsdValue = 0;

                if (data.recoveries.length === 0) {
                    recoveryTableBody.innerHTML = `
                        <tr><td colspan="4" class="text-center py-3 text-gray-500">
                            Belum ada data recovery
                        </td></tr>`;
                    totalRp.textContent = 'Rp 0';
                    totalUsd.textContent = 'USD 0.00';
                    return;
                }

                // ✅ Loop isi tabel
                data.recoveries.forEach(r => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border px-4 py-2">${r.item}</td>
                        <td class="border px-4 py-2 text-right">Rp ${r.nilai}</td>
                        <td class="border px-4 py-2 text-right">USD ${r.usd}</td>
                        <td class="border px-4 py-2 text-center">
                            <button class="delete-recovery text-red-500 hover:text-red-700" data-id="${r.id}">🗑️</button>
                        </td>
                    `;
                    recoveryTableBody.appendChild(row);

                    totalRpValue += parseFloat(r.nilai.replace(/\./g, '').replace(',', '.'));
                    totalUsdValue += parseFloat(r.usd);
                });

                totalRp.textContent = formatRp(totalRpValue);
                totalUsd.textContent = formatUsd(totalUsdValue);
            }

      
            /* ===================== ADD RECOVERY ===================== */
            if (addRecoveryBtn) {
                addRecoveryBtn.addEventListener('click', async () => {
                    const item = recoveryName.value.trim();
                    const nilai = parseFloat(recoveryValue.value);
                    if (!item || isNaN(nilai)) return alert('Isi Recovery Name dan Nilai dengan benar.');
                    
                    // ✅ Gunakan variabel dari scope luar (closure)
                    if (!currentAssessmentId) return alert('Pilih assessment terlebih dahulu.');

                    const res = await fetch(`/admin/recovery/${currentAssessmentId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ item, nilai }),
                    });

                    const data = await res.json().catch(() => null);
                    if (!data?.success) return alert('Gagal menambah recovery.');

                    recoveryName.value = '';
                    recoveryValue.value = '';

                    // reload tabel setelah tambah
                    await loadRecoveries(currentAssessmentId); // ✅ Gunakan langsung
                });
            }

            /* ===================== DELETE RECOVERY ===================== */
            recoveryTableBody?.addEventListener('click', async (e) => {
                if (!e.target.classList.contains('delete-recovery')) return;

                const id = e.target.dataset.id;
                if (!confirm('Hapus item ini?')) return;

                const res = await fetch(`/admin/recovery/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });

                const data = await res.json().catch(() => null);
                if (!data?.success) return alert('Gagal menghapus recovery.');

                // reload tabel
                await loadRecoveries(currentAssessmentId);
            });

            /* ✅ Registrasikan fungsi agar bisa dipanggil dari script utama */
            window.loadRecoveries = loadRecoveries;
            </script>


        </div>
    </div>


</x-app-layout>