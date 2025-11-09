<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ __('Auditor Dashboard') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Welcome back, Here's a summary of audit findings.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Total Findings</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $totalFindings }}">0</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Outstanding Findings</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $outstandingFindings }}">0</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Auditee</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $auditeeCount }}">0</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Team Member</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $teamMemberCount }}">0</p>
                </div>
            </div>

            <div class="group flex items-center py-3">
                <span class="mr-2">ALL CHART:</span>
                <div class="divider-interactive flex-1 h-px bg-gray-700 group-hover:bg-gradient-to-r group-hover:from-red-500 group-hover:via-transparent group-hover:to-red-500 transition-all duration-300"></div>
            </div>

            <!-- Filter Waktu - UI Diperhalus: Rounded Lembut, Warna Gelap Elegan -->
            <div class="w-full flex justify-center mt-4 mb-6">
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 p-3 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <!-- Grup Input Tanggal -->
                    <div class="flex items-center gap-3 bg-white dark:bg-gray-700 p-2.5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
                        <!-- From Date -->
                        <div class="relative flex items-center">
                            <input
                                type="date"
                                id="date_from"
                                class="py-1.5 bg-transparent text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-0 w-36 font-medium"
                            />
                        </div>

                        <!-- Pemisah: Tanda "-" -->
                        <span class="text-gray-500 dark:text-gray-400 font-medium">-</span>

                        <!-- To Date -->
                        <div class="relative flex items-center">
                            <input
                                type="date"
                                id="date_to"
                                class="py-1.5 bg-transparent text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-0 w-36 font-medium"
                            />
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <button
                        id="apply_date_filter"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition duration-200 flex items-center gap-2 text-sm shadow-md"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Apply</span>
                    </button>

                    <!-- Reset Button -->
                    <button
                        id="reset_date_filter"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition duration-200 flex items-center gap-2 text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
            
            <div class="w-full p-4">

                <!-- Container khusus untuk 3 chart -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="audit-charts-container">
                    <!-- Case 1: Stacked Bar Chart (Fin Loss) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-96 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Fin Loss (Recovery & Non)</h3>
                        </div>
                        <div class="flex-1 p-4">
                            <canvas id="chartFinLoss" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Case 2: Multi-line Chart (Improvement) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-96 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Improvement</h3>
                        </div>
                        <div class="flex-1 p-4">
                            <canvas id="chartImprovement" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Case 3: Heatmap (Non-Compliance) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-96 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Non-Compliance</h3>
                        </div>
                        <div class="flex-1 p-4">
                            <div id="chartNonCompliance" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ======================================================
                // 1. ANIMASI COUNTER DI ATAS DASHBOARD
                // ======================================================
                const counters = document.querySelectorAll('.count-up');
                const speed = 100;
                counters.forEach(counter => {
                    const animate = () => {
                        const target = +counter.dataset.target;
                        const current = +counter.innerText;
                        const increment = Math.ceil(target / speed);
                        if (current < target) {
                            counter.innerText = current + increment;
                            setTimeout(animate, 15);
                        } else {
                            counter.innerText = target.toLocaleString('id-ID');
                        }
                    };
                    animate();
                });

                // ======================================================
                // 2. UPDATE SEMUA CHART (DENGAN PARAM FILTER)
                // ======================================================

                function updateAllCharts(from = null, to = null) {
                    const params = from && to ? `?from=${from}&to=${to}` : '';
                    console.log("Fetching charts with params:", params);

                    // 2.1 Fin Loss Chart (Donut)
                    // ===================================================================

                    const ctxFinLoss = document.getElementById('chartFinLoss').getContext('2d');
                    fetch(`/admin/dashboard/data/finloss${params}`)
                        .then(res => res.json())
                        .then(data => {

                            if (window.finLossChart) window.finLossChart.destroy();

                            // --- LANGKAH 1: SIAPKAN TEKS SUBTITLE SECARA DINAMIS ---
                            let subtitleText = 'Showing default period'; // Teks default saat tidak ada filter

                            if (from && to) {
                                // Fungsi kecil untuk memformat tanggal agar lebih mudah dibaca
                                const formatDate = (dateString) => {
                                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                                    return new Date(dateString).toLocaleDateString('id-ID', options);
                                };
                                
                                subtitleText = `Period: ${formatDate(from)} - ${formatDate(to)}`;
                            }
                            // -------------------------------------------------------------

                            window.finLossChart = new Chart(ctxFinLoss, {
                                type: 'doughnut',
                                data: {
                                    labels: ['High', 'Medium', 'Low'],
                                    datasets: [
                                        {
                                            label: 'Recovery',
                                            data: data.Recovery,
                                            backgroundColor: ['#b91c1c', '#ea580c', '#16a34a'],
                                            borderColor: '#fff',
                                            borderWidth: 2
                                        },
                                        {
                                            label: 'Non-Recovery',
                                            data: data['Non-Recovery'],
                                            backgroundColor: ['#ef4444', '#f97316', '#22c55e'],
                                            borderColor: '#fff',
                                            borderWidth: 2
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '70%',
                                    layout: {
                                        padding: {
                                            top: 10,
                                            bottom: 20
                                        }
                                    },
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Total Fin Loss Composition',
                                            font: { size: 16 },
                                            padding: {
                                                // Kurangi padding bawah judul agar lebih dekat ke subtitle
                                                bottom: 5 
                                            }
                                        },
                                        // --- LANGKAH 2: TAMBAHKAN KONFIGURASI SUBTITLE ---
                                        subtitle: {
                                            display: true,
                                            text: subtitleText, // Gunakan variabel dinamis yang sudah kita buat
                                            color: '#6b7280', // Warna abu-abu agar tidak terlalu menonjol
                                            font: {
                                                size: 12,
                                                weight: 'normal'
                                            },
                                            padding: {
                                                bottom: 15 // Jarak antara subtitle dan grafik donat
                                            }
                                        },
                                        // ---------------------------------------------------
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                color: '#374151',
                                                font: { size: 12 },
                                                boxWidth: 12,
                                                padding: 20
                                            }
                                        }
                                    }
                                }
                            });
                        });

                    
                    // 2.2 Improvement Chart (Stacked Bar)
                    const ctxImprovement = document.getElementById('chartImprovement').getContext('2d');
                    fetch(`/admin/dashboard/data/improvement${params}`)
                        .then(res => res.json())
                        .then(data => {

                            if (window.improvementChart) window.improvementChart.destroy();

                            window.improvementChart = new Chart(ctxImprovement, {
                                type: 'bar',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        { label: 'High', data: data.High, backgroundColor: '#ef4444' },
                                        { label: 'Medium', data: data.Medium, backgroundColor: '#f97316' },
                                        { label: 'Low', data: data.Low, backgroundColor: '#22c55e' }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: { display: true, text: 'Improvement Findings by Priority' },
                                        legend: { position: 'top' }
                                    },
                                    scales: {
                                        x: { stacked: true },
                                        y: { stacked: true, beginAtZero: true }
                                    }
                                }
                            });
                        });

                    //2.3
                    fetch(`/admin/dashboard/data/noncompliance${params}`)
    .then(res => res.json())
    .then(result => {
        const rawData = result.data;

        // ✅ Urutkan data berdasarkan urutan bulan
        const monthOrder = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        rawData.sort((a, b) => monthOrder.indexOf(a.x) - monthOrder.indexOf(b.x));

        const levels = ['High', 'Medium', 'Low'];

        // ✅ Warna tetap per level
        const colorByLevel = {
            High: '#ef4444',   // merah
            Medium: '#f97316', // oranye
            Low: '#22c55e'     // hijau
        };

        // ✅ Susun data per level
        const seriesData = levels.map(level => ({
            name: level,
            data: rawData
                .filter(item => item.y === level)
                .map(item => ({
                    x: item.x,
                    y: item.value // biarkan nilai 0 tetap ada
                })),
            color: colorByLevel[level]
        }));

        // ✅ Cek apakah semua data kosong
        const total = rawData.reduce((acc, item) => acc + item.value, 0);
        const isEmpty = total === 0 || seriesData.every(s => s.data.length === 0);
        const finalSeries = isEmpty ? [] : seriesData;

        if (window.nonComplianceChart) window.nonComplianceChart.destroy();

        // ✅ Konfigurasi chart
        const options = {
            series: finalSeries,
            chart: {
                type: 'heatmap',
                height: '100%',
                toolbar: { show: false },
                animations: { enabled: false }
            },
            plotOptions: {
                heatmap: {
                    distributed: false,
                    shadeIntensity: 0.5,
                    colorScale: {
                        // ✅ Semua kategori dan rentang nilai
                        ranges: [
                            { from: 0, to: 0, color: '#e5e7eb', name: 'Zero' },   // abu-abu terang
                            { from: 1, to: 3, color: '#22c55e', name: 'Low' },     // hijau
                            { from: 4, to: 5, color: '#f97316', name: 'Medium' },  // oranye
                            { from: 6, to: 9999, color: '#ef4444', name: 'High' }  // merah
                        ]
                    }
                }
            },
            dataLabels: {
                enabled: true,
                style: { colors: ['#000'] }, // hitam agar kontras di semua warna
                formatter: val => (val === 0 ? '0' : val) // tampilkan angka "0"
            },
            yaxis: { categories: ['High', 'Medium', 'Low'] },
            xaxis: {
                type: 'category',
                labels: {
                    rotate: -35, // 🔄 miringkan label
                    style: {
                    fontSize: '11px', // perkecil font biar gak nabrak
                    },
                    formatter: val => {
                    const date = new Date(val + '-01');
                    // tampilkan singkat: "Sep '25"
                    return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
                    }
                }
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'center',
                labels: { colors: '#374151' }
            },
            noData: {
                text: 'No Data Available',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#9ca3af',
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            }
        };

        window.nonComplianceChart = new ApexCharts(
            document.querySelector("#chartNonCompliance"),
            options
        );
        setTimeout(() => window.nonComplianceChart.render(), 50);
    });




                }

                // ======================================================
                // 3. EVENT HANDLER UNTUK FILTER
                // ======================================================
                document.getElementById('apply_date_filter').addEventListener('click', () => {
                    
                    const from = document.getElementById('date_from').value;
                    const to = document.getElementById('date_to').value;
                    if (!from || !to) {
                        alert('Silakan pilih rentang tanggal terlebih dahulu.');
                        return;
                    }
                    updateAllCharts(from, to);
                });

                document.getElementById('reset_date_filter').addEventListener('click', () => {
                    document.getElementById('date_from').value = '';
                    document.getElementById('date_to').value = '';
                    updateAllCharts(); // kembali ke default
                });

                // ======================================================
                // 4. LOAD CHART PERTAMA KALI
                // ======================================================
                // Plugin global Chart.js untuk menampilkan teks "No Data"
                Chart.register({
                    id: 'noDataText',
                    afterDraw(chart) {
                        const datasets = chart.data.datasets;
                        if (datasets.length === 0 || datasets.every(ds => ds.data.every(v => v === 0))) {
                            const { ctx, chartArea } = chart;
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#9ca3af'; // warna abu-abu
                            ctx.font = 'bold 16px sans-serif';
                            ctx.fillText('No Data Available', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                            ctx.restore();
                        }
                    }
                });

                updateAllCharts(); // default: ±2 bulan dari sekarang

                
                // ===== Fin Loss - Recovery Multi-line Chart =====
                const ctxRecovery = document.getElementById('finLossRecoveryChart').getContext('2d');
                const formatRp = val => 'Rp ' + (Number(val) || 0).toLocaleString('id-ID');
                const formatUsd = val => '$' + (Number(val) || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });

                async function loadFinLossRecoveryChart(from = null, to = null, viewType = 'all') {
                    const params = new URLSearchParams();
                    if (from && to) {
                        params.append('from', from);
                        params.append('to', to);
                    }

                    const container = document.getElementById('auditFormList');
                    container.innerHTML = '<p class="text-gray-500 italic animate-pulse">Loading data...</p>';

                    try {
                        const res = await fetch("{{ route('dashboard.finloss-recovery') }}?" + params.toString());
                        if (!res.ok) throw new Error('Fetch failed with status ' + res.status);
                        const result = await res.json();

                        // Tentukan total sesuai viewType
                        let totalRp = result.totals.recovery_rp;
                        let totalUsd = result.totals.recovery_usd;
                        if (viewType === 'finloss') {
                            totalRp = result.totals.finloss_rp;
                            totalUsd = result.totals.finloss_usd;
                        } else if (viewType === 'restinput') {
                            totalRp = result.totals.restinput_rp;
                            totalUsd = result.totals.restinput_usd;
                        } else if (viewType === 'restoutput') {
                            totalRp = result.totals.restoutput_rp;
                            totalUsd = result.totals.restoutput_usd;
                        }

                        document.querySelector('#totalRecoveryRp').textContent = formatRp(totalRp);
                        document.querySelector('#totalRecoveryUsd').textContent = formatUsd(totalUsd);

                        let labelText = 'Total Recovery Amount';
                        if (viewType === 'finloss') labelText = 'Total Fin Loss Amount';
                        else if (viewType === 'restinput') labelText = 'Total Rest Input Amount';
                        else if (viewType === 'restoutput') labelText = 'Total Rest Output Amount';
                        document.querySelector('#totalLabel').textContent = labelText;

                        // Filter dataset sesuai viewType
                        let selectedSeries = result.series;
                        if (viewType !== 'all') {
                            const mapping = {
                                finloss: 'Fin Loss',
                                restinput: 'Rest Input',
                                restoutput: 'Rest Output'
                            };
                            selectedSeries = result.series.filter(s => s.name === mapping[viewType]);
                        }

                        // Render Chart
                        if (window.finLossRecoveryChart instanceof Chart) {
                            window.finLossRecoveryChart.destroy();
                        }

                        window.finLossRecoveryChart = new Chart(ctxRecovery, {
                            type: 'line',
                            data: {
                                labels: result.labels,
                                datasets: selectedSeries.map((s, idx) => ({
                                    label: s.name,
                                    data: s.data,
                                    borderColor: ['#ef4444', '#059669', '#3b82f6', '#a855f7'][idx % 4],
                                    backgroundColor: ['rgba(239,68,68,0.1)', 'rgba(5,150,105,0.1)', 'rgba(59,130,246,0.1)', 'rgba(168,85,247,0.1)'][idx % 4],
                                    tension: 0.3,
                                    fill: false
                                }))
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } },
                                scales: { y: { beginAtZero: true } }
                            }
                        });

                        // Audit Links
                        container.innerHTML = ''; // kosongkan loading text

                        if (Object.keys(result.audit_links).length === 0) {
                            container.innerHTML = '<p class="text-gray-500 italic">No data available for this period.</p>';
                        } else {
                            Object.entries(result.audit_links).forEach(([date, forms]) => {
                                const section = document.createElement('div');
                                section.className = 'border-b border-gray-200 dark:border-gray-600 pb-2 last:border-0 last:pb-0';
                                section.innerHTML = `
                                    <p class="font-medium text-gray-800 dark:text-gray-200">${date}:</p>
                                    <ul class="mt-1 space-y-1">
                                        ${forms.map(f => `
                                            <li>
                                                <a href="/admin/findings/${f.id}/assessment"
                                                class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 hover:underline transition-colors">
                                                    ${f.title || '(No Title)'} #${f.id}
                                                </a>
                                            </li>
                                        `).join('')}
                                    </ul>`;
                                container.appendChild(section);
                            });
                        }

                    } catch (error) {
                        console.error('[ERROR] FinLoss-Recovery:', error);
                        container.innerHTML = '<p class="text-red-500 italic">Failed to load data.</p>';
                    }
                }

                // === Filter logic ===
                // ▼▼▼ Gunakan ID yang baru dan unik ▼▼▼
                const fromInput = document.getElementById('date_from_finloss');
                const toInput = document.getElementById('date_to_finloss');
                const viewSelect = document.getElementById('view_filter');
                const applyBtn = document.getElementById('apply_finloss_filter');
                const resetBtn = document.getElementById('reset_filter_finloss'); // Ubah juga ID reset button

                function getFilters() {
                    return {
                        from: fromInput.value || null,
                        to: toInput.value || null,
                        type: viewSelect.value || 'all'
                    };
                }

                // --- Trigger saat tombol APPLY di klik ---
                applyBtn.addEventListener('click', () => {
                    const { from, to, type } = getFilters();
                    // Validasi: pastikan kedua tanggal sudah diisi
                    if (!from || !to) {
                        alert('Silakan pilih rentang tanggal (Start Date & End Date) terlebih dahulu.');
                        return; // Hentikan eksekusi jika tanggal tidak lengkap
                    }
                    // Jika valid, panggil fungsi untuk update chart dengan parameter
                    loadFinLossRecoveryChart(from, to, type);
                });


                // --- Ganti tipe view (All / FinLoss / RestInput / RestOutput) ---
                viewSelect.addEventListener('change', () => {
                    const { from, to, type } = getFilters();
                    loadFinLossRecoveryChart(from, to, type);
                });

                // --- Reset ke default 1 tahun terakhir ---
                resetBtn.addEventListener('click', () => {
                    fromInput.value = '';
                    toInput.value = '';
                    viewSelect.value = 'all';
                    document.querySelector('#totalLabel').textContent = 'Total Recovery Amount';
                    loadFinLossRecoveryChart(); // Panggil tanpa parameter untuk me-load data default
                });

                loadFinLossRecoveryChart(); // load default saat pertama

            });
            </script>
            @endpush


            <!-- Fin Loss - Recovery Section -->
            <div class="mt-10 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                <!-- Header -->
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 animate-fade-in-up">
                    Fin Loss - Recovery
                </h2>

                <!-- Statistik Uang + Filter Controls -->
                <div class="flex flex-col lg:flex-row lg:items-end gap-4 mb-6 animate-fade-in-up">

                    <!-- Statistik Uang -->
                    <div class="lg:w-1/3 flex flex-col justify-center">
                        <div class="space-y-2">
                            <p id="totalRecoveryRp"
                            class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-teal-300">
                            Rp 0
                            </p>
                            <p id="totalLabel" class="text-sm text-gray-600 dark:text-gray-400">Total Recovery Amount</p>
                            <p id="totalRecoveryUsd" class="text-xs text-gray-500 dark:text-gray-500 font-extrabold">$0.00 USD</p>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div class="lg:w-2/3 flex flex-wrap gap-3 items-end">
                        @php
                            $inputClass = "w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all";
                        @endphp

                        <!-- Start Date -->
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                             <input id="date_from_finloss" type="date" class="{{ $inputClass }}">
                        </div>

                        <!-- End Date -->
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input id="date_to_finloss" type="date" class="{{ $inputClass }}">
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">View Type</label>
                            <select id="view_filter" class="{{ $inputClass }}">
                                <option value="all" selected>All (Multi-line)</option>
                                <option value="finloss">Total Fin Loss</option>
                                <option value="restinput">Rest Input</option>
                                <option value="restoutput">Rest Output</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                            <button id="apply_finloss_filter"
                                class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-all border border-transparent">
                                Apply
                            </button>
                        </div>

                        <!-- Reset Button -->
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                            <button id="reset_filter_finloss"
                            class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-800/50 
                                text-indigo-700 dark:text-indigo-300 font-medium rounded-md transition-all border 
                                border-gray-300 dark:border-gray-600">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Chart + Audit Panel -->
                <div class="flex flex-col lg:flex-row gap-6">

                    <!-- Chart Area -->
                    <div class="lg:w-2/3 flex flex-col animate-fade-in-up">
                        <div class="h-[300px] relative bg-gray-50 dark:bg-gray-750 rounded-lg border border-gray-200 dark:border-gray-600 p-1">
                            <canvas id="finLossRecoveryChart" class="w-full h-full rounded"></canvas>
                        </div>

                        <!-- Legend Horizontal - Lebih Ringkas & Warna Lebih Halus -->
                        <div class="flex flex-wrap justify-center gap-5 mt-4 text-sm">
                            @foreach([['Fin Loss', 'bg-red-500'], ['Recovery', 'bg-emerald-500'], ['Rest Output', 'bg-blue-500'], ['Rest Input', 'bg-purple-500']] as [$label, $color])
                                <div class="flex items-center gap-2">
                                    <div class="{{ $color }} w-3 h-3 rounded-sm"></div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Panel Audit Form - Lebih Menarik -->
                    <div class="lg:w-80 bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600 h-[300px] overflow-y-auto animate-fade-in-up">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Audit Form Links
                        </h3>
                        <div id="auditFormList" class="space-y-3 text-xs">
                            @php
                                $auditEntries = [
                                    ['date' => '30 - 10 - 2025', 'ids' => ['A001', 'A002']],
                                    ['date' => '15 - 09 - 2025', 'ids' => ['A003']],
                                    ['date' => '05 - 08 - 2025', 'ids' => ['A004', 'A005']]
                                ];
                            @endphp

                            @foreach($auditEntries as $entry)
                                <div class="border-b border-gray-200 dark:border-gray-600 pb-2 last:border-0 last:pb-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $entry['date'] }}:</p>
                                    <ul class="mt-1 space-y-1">
                                        @foreach($entry['ids'] as $id)
                                            <li>
                                                <a href="#" 
                                                class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 hover:underline transition-colors">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                                    AuditForm #{{ $id }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>          

        </div>
    </div>
</x-app-layout>