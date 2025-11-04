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

            <!-- Filter Waktu - UI Diperhalus: Rounded Lembut, Warna Gelap Elegan -->
            <div class="w-full flex justify-center mt-8 mb-6">
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

           @push('scripts')
            <!-- Load Chart.js & ApexCharts dari CDN -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const counters = document.querySelectorAll('.count-up');
                const speed = 100; // Semakin besar = semakin lambat

                counters.forEach(counter => {
                    const animate = () => {
                        const target = +counter.getAttribute('data-target');
                        const current = +counter.innerText;
                        const increment = Math.ceil(target / speed);

                        if (current < target) {
                            counter.innerText = current + increment;
                            setTimeout(animate, 15);
                        } else {
                            counter.innerText = target.toLocaleString('id-ID'); // Format angka ribuan
                        }
                    };
                    animate();
                });

                // ===== CASE 1: Multi-level Donut Chart (Fin Loss) [DIUBAH] =====
                const ctxFinLoss = document.getElementById('chartFinLoss').getContext('2d');

                fetch('/admin/dashboard/data/finloss')
                .then(res => res.json())
                .then(data => {
                    console.log('FinLoss data:', data);

                    if (!data.Recovery || !data['Non-Recovery']) {
                        console.warn('FinLoss data kosong atau tidak valid');
                        return;
                    }

                    const labels = ['High', 'Medium', 'Low'];

                    new Chart(ctxFinLoss, {
                        type: 'doughnut',
                        data: {
                            labels,
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
                            cutout: '65%',
                            
                            // DITAMBAHKAN KEMBALI: Ini adalah bagian yang hilang
                            layout: {
                                padding: 20 // Memberi margin internal agar donat lebih kecil
                            },

                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    align: 'center',
                                    // Kustomisasi label legenda (opsional tapi disarankan)
                                    labels: {
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                const dataset = data.datasets[1]; // Ambil warna dari ring luar
                                                return data.labels.map((label, i) => ({
                                                    text: label,
                                                    fillStyle: dataset.backgroundColor[i],
                                                    strokeStyle: '#000000',
                                                    lineWidth: 1,
                                                    hidden: isNaN(dataset.data[i]),
                                                    index: i
                                                }));
                                            }
                                            return [];
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Total Fin Loss Composition',
                                    font: { size: 16 }
                                },
                                subtitle: {
                                    display: true,
                                    text: 'Default (±2 bulan dari sekarang)',
                                    font: { size: 12 },
                                    color: '#6b7280'
                                },
                                // Tooltip kustom (opsional tapi disarankan)
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const datasetLabel = context.dataset.label || '';
                                            const label = context.label || '';
                                            const value = context.raw;
                                            return `${datasetLabel} (${label}): ${value}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });

                // ===== CASE 2: Stacked Bar Chart (Improvement) [DIUBAH] =====
                const ctxImprovement = document.getElementById('chartImprovement').getContext('2d');

                fetch('/admin/dashboard/data/improvement')
                .then(res => res.json())
                .then(data => {
                    const datasets = [
                        {
                            label: 'High',
                            data: data.High,
                            backgroundColor: '#ef4444',
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Medium',
                            data: data.Medium,
                            backgroundColor: '#f97316',
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Low',
                            data: data.Low,
                            backgroundColor: '#22c55e',
                            stack: 'Stack 0'
                        }
                    ];

                    new Chart(ctxImprovement, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: { mode: 'index', intersect: false },
                                title: {
                                    display: true,
                                    text: 'Improvement Findings by Priority'
                                }
                            },
                            scales: {
                                x: { stacked: true },
                                y: { stacked: true, beginAtZero: true }
                            }
                        }
                    });
                })
                .catch(err => console.error('Improvement chart error:', err));


                // ===== CASE 3: Heatmap (Non-Compliance) [TETAP SAMA SEPERTI SEBELUMNYA] =====
                fetch('/admin/dashboard/data/noncompliance')
                .then(res => res.json())
                .then(result => {
                    const rawData = result.data;

                    // Susun data berdasarkan Y (priority)
                    const levels = ['High', 'Medium', 'Low'];
                    const seriesData = levels.map(level => {
                        return {
                            name: level,
                            data: rawData
                                .filter(item => item.y === level)
                                .map(item => ({ x: item.x, y: item.value }))
                        };
                    });

                    const options = {
                    series: seriesData,
                    chart: {
                        type: 'heatmap',
                        height: '100%',
                        toolbar: { show: false }
                    },
                    colors: ['#ef4444', '#f97316', '#22c55e'], // High, Medium, Low
                    plotOptions: {
                        heatmap: {
                        distributed: true,
                        shadeIntensity: 0.5,
                        useFillColorAsStroke: true
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: { colors: ['#000'] },
                        formatter: val => (val === 0 ? '' : val)
                    },
                    yaxis: {
                        categories: ['High', 'Medium', 'Low']
                    },
                    tooltip: {
                        y: { formatter: val => `${val} cases` }
                    }
                    };


                    const heatmapChart = new ApexCharts(document.querySelector("#chartNonCompliance"), options);
                    heatmapChart.render();
                })
                .catch(err => console.error('Non-Compliance chart error:', err));
            });

            // ===== Fin Loss - Recovery Multi-line Chart =====
            const ctxRecovery = document.getElementById('finLossRecoveryChart').getContext('2d');
            new Chart(ctxRecovery, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Fin Loss',
                            data: [120, 150, 130, 160, 180, 200],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Recovery',
                            data: [80, 90, 100, 110, 130, 150],
                            borderColor: '#059669', // emerald-700
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Rest Output',
                            data: [40, 50, 45, 60, 70, 80],
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Rest Input',
                            data: [30, 35, 40, 50, 60, 65],
                            borderColor: '#a855f7', // purple-500
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.3,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // karena pakai custom legend
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
            </script>
            @endpush

            <!-- Fin Loss - Recovery Section -->
            <!-- Fin Loss - Recovery Section -->
<!-- Fin Loss - Recovery Section -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
    <!-- Header -->
    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 animate-fade-in-up">
        Fin Loss - Recovery
    </h2>

    <!-- Statistik Uang + Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-end gap-4 mb-6 animate-fade-in-up">

        <!-- Statistik Uang - Dengan Gradient & Shadow Halus -->
        <div class="lg:w-1/3 flex flex-col justify-center">
            <div class="space-y-2">
                <p class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-teal-300">
                    Rp 1.25M
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Recovery Amount</p>
                <p class="text-xs text-gray-500 dark:text-gray-500">$1,250,000 USD</p>
            </div>
        </div>

        <!-- Filter Controls - Lebih Berwarna & Interaktif -->
        <div class="lg:w-2/3 flex flex-wrap gap-3 items-end">
            <!-- Helper: buat reusable class kalau perlu -->
            @php
                $inputClass = "w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all";
            @endphp

            <div class="flex flex-col">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" class="{{ $inputClass }}">
            </div>

            <div class="flex flex-col">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" class="{{ $inputClass }}">
            </div>

            <div class="flex flex-col">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter 1</label>
                <select class="{{ $inputClass }}">
                    <option>All</option>
                    <option>Option A</option>
                    <option>Option B</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter 2</label>
                <select class="{{ $inputClass }}">
                    <option>All</option>
                    <option>Category X</option>
                    <option>Category Y</option>
                </select>
            </div>

            <!-- Filter 3 + Reset -->
            <div class="flex flex-col">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter 3</label>
                <div class="flex gap-2">
                    <select class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all">
                        <option>All</option>
                        <option>Status Active</option>
                        <option>Status Archived</option>
                    </select>
                    <button class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-800/50 text-indigo-700 dark:text-indigo-300 font-medium rounded-r-md transition-all border border-gray-300 dark:border-gray-600">
                        Reset
                    </button>
                </div>
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