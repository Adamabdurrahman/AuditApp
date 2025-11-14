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
                    <p class="text-sm text-green-800 dark:text-green-300">Total Pertemuan</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $totalPertemuan }}">0</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Total Engagement</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2 count-up" data-target="{{ $totalEngagement }}">0</p>
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col gap-2">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Fin Loss (Recovery & Non)</h3>
                                <div class="flex flex-wrap items-end gap-2 sm:gap-3 text-xs">
                                    <label for="finloss-donut-start" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Start Date</span>
                                        <input type="date" id="finloss-donut-start" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                                    </label>
                                    <span class="text-gray-400 pb-2 sm:pb-1">-</span>
                                    <label for="finloss-donut-end" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">End Date</span>
                                        <input type="date" id="finloss-donut-end" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                                    </label>
                                    <div class="flex items-end gap-2">
                                        <button id="finloss-donut-apply" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded transition">Apply</button>
                                        <button id="finloss-donut-reset" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded transition">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 p-4">
                            <canvas id="chartFinLoss" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Case 2: Multi-line Chart (Improvement) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col gap-2">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Improvement</h3>
                                <div class="flex flex-wrap items-end gap-2 sm:gap-3 text-xs">
                                    <label for="improvement-start" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Start Date</span>
                                        <input type="date" id="improvement-start" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                    </label>
                                    <span class="text-gray-400 pb-2 sm:pb-1">-</span>
                                    <label for="improvement-end" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">End Date</span>
                                        <input type="date" id="improvement-end" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                    </label>
                                    <div class="flex items-end gap-2">
                                        <button id="improvement-apply" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded transition">Apply</button>
                                        <button id="improvement-reset" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded transition">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 p-4">
                            <canvas id="chartImprovement" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Case 3: Heatmap (Non-Compliance) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col gap-2">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Non-Compliance</h3>
                                <div class="flex flex-wrap items-end gap-2 sm:gap-3 text-xs">
                                    <label for="noncompliance-start" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Start Date</span>
                                        <input type="date" id="noncompliance-start" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-orange-500" />
                                    </label>
                                    <span class="text-gray-400 pb-2 sm:pb-1">-</span>
                                    <label for="noncompliance-end" class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">End Date</span>
                                        <input type="date" id="noncompliance-end" class="min-w-[130px] px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-orange-500" />
                                    </label>
                                    <div class="flex items-end gap-2">
                                        <button id="noncompliance-apply" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded transition">Apply</button>
                                        <button id="noncompliance-reset" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded transition">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 p-4">
                            <div id="chartNonCompliance" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finding Status Chart Section -->
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Finding Status</h3>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <input type="date" id="status-start-date" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            <span class="text-gray-500 dark:text-gray-400">-</span>
                            <input type="date" id="status-end-date" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            <button id="status-apply" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded transition text-xs">Apply</button>
                        </div>
                    </div>
                    <div class="p-6 relative h-[360px]">
                        <canvas id="chartStatusCircle" class="w-full h-full"></canvas>
                        <p id="status-chart-message" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400 px-6">
                            No data for this period.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Audit Engagement Section -->
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Audit Engagement</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Distribution of findings per engagement.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <label for="audit-engagement-year" class="font-semibold text-gray-600 dark:text-gray-300">Year</label>
                            <select id="audit-engagement-year" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="relative h-[360px]">
                                <canvas id="auditEngagementChart" class="w-full h-full"></canvas>
                                <p id="auditEngagementMessage" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400 px-6">
                                    No engagement data available.
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Engagement Details</h4>
                                <ul id="auditEngagementDetails" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="text-gray-400 dark:text-gray-500">Loading data...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                            counter.innerText = target.toLocaleString('id-ID');
                        }
                    };
                    animate();
                });

                const playWelcomeMessage = () => {
                    if (typeof window === 'undefined' || !('speechSynthesis' in window)) return;

                    const STORAGE_KEY = 'dashboardWelcomePlayedV3';
                    if (sessionStorage.getItem(STORAGE_KEY)) return;

                    const synth = window.speechSynthesis;
                    let hasSpoken = false;

                    const markPlayed = () => {
                        sessionStorage.setItem(STORAGE_KEY, 'true');
                        window.removeEventListener('click', handleUserInteraction);
                        window.removeEventListener('touchstart', handleUserInteraction);
                    };

                    const utterance = new SpeechSynthesisUtterance('Welcome to the audit website');
                    utterance.lang = 'en-US';
                    utterance.rate = 0.9;
                    utterance.pitch = 1.2;
                    utterance.volume = 0.9;
                    utterance.onend = markPlayed;
                    utterance.onerror = markPlayed;

                    const selectVoice = () => {
                        const voices = synth.getVoices();
                        if (!voices?.length) return false;

                        const preferred = voices.find(voice =>
                            voice.name.includes('Female') ||
                            voice.name.includes('female') ||
                            voice.name.includes('Google US English') ||
                            voice.name.includes('Microsoft Zira')
                        ) || voices.find(voice => /en-US|en-GB/i.test(voice.lang)) || voices[0];

                        if (preferred) {
                            utterance.voice = preferred;
                        }
                        return true;
                    };

                    const attemptSpeak = () => {
                        if (hasSpoken || sessionStorage.getItem(STORAGE_KEY)) return;

                        const startSpeaking = () => {
                            synth.cancel();
                            synth.speak(utterance);
                            hasSpoken = true;
                        };

                        if (!selectVoice()) {
                            synth.addEventListener('voiceschanged', () => {
                                if (selectVoice()) {
                                    startSpeaking();
                                }
                            }, { once: true });
                        } else {
                            startSpeaking();
                        }
                    };

                    const handleUserInteraction = () => {
                        attemptSpeak();
                    };

                    if (document.readyState === 'complete') {
                        setTimeout(attemptSpeak, 300);
                    } else {
                        window.addEventListener('load', () => setTimeout(attemptSpeak, 300), { once: true });
                    }

                    window.addEventListener('click', handleUserInteraction, { once: true });
                    window.addEventListener('touchstart', handleUserInteraction, { once: true });
                };

                playWelcomeMessage();

                const buildUrlWithRange = (path, startValue, endValue) => {
                    const url = new URL(path, window.location.origin);
                    if (startValue) {
                        url.searchParams.append('start_date', startValue);
                    }
                    if (endValue) {
                        url.searchParams.append('end_date', endValue);
                    }
                    return url.toString();
                };

                // ===== CASE 1: Multi-level Donut Chart (Fin Loss) =====
                const ctxFinLoss = document.getElementById('chartFinLoss')?.getContext('2d');
                let finLossChartInstance = null;
                const finlossDonutStart = document.getElementById('finloss-donut-start');
                const finlossDonutEnd = document.getElementById('finloss-donut-end');
                const finlossDonutApply = document.getElementById('finloss-donut-apply');
                const finlossDonutReset = document.getElementById('finloss-donut-reset');

                const renderFinLossChart = (data) => {
                    if (!ctxFinLoss) return;

                    if (!data?.Recovery || !data['Non-Recovery']) {
                        console.warn('FinLoss data kosong atau tidak valid');
                        return;
                    }

                    if (finLossChartInstance) {
                        finLossChartInstance.destroy();
                        finLossChartInstance = null;
                    }

                    const labels = ['High', 'Medium', 'Low'];

                    const startDisplay = finlossDonutStart?.value;
                    const endDisplay = finlossDonutEnd?.value;

                    finLossChartInstance = new Chart(ctxFinLoss, {
                        type: 'doughnut',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Recovery',
                                    data: data.Recovery,
                                    backgroundColor: ['#b91c1c', '#ea580c', '#16a34a'],
                                    borderColor: '#fff',
                                    borderWidth: 2,
                                },
                                {
                                    label: 'Non-Recovery',
                                    data: data['Non-Recovery'],
                                    backgroundColor: ['#ef4444', '#f97316', '#22c55e'],
                                    borderColor: '#fff',
                                    borderWidth: 2,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            layout: { padding: 20 },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    align: 'center',
                                    labels: {
                                        generateLabels(chart) {
                                            const dataset = chart.data.datasets?.[1];
                                            if (!dataset) return [];
                                            return chart.data.labels.map((label, index) => ({
                                                text: label,
                                                fillStyle: dataset.backgroundColor[index],
                                                strokeStyle: '#000000',
                                                lineWidth: 1,
                                                hidden: isNaN(dataset.data[index]),
                                                index,
                                            }));
                                        },
                                    },
                                },
                                title: {
                                    display: true,
                                    text: 'Total Fin Loss Composition',
                                    font: { size: 16 },
                                },
                                subtitle: {
                                    display: true,
                                    text: startDisplay || endDisplay
                                        ? 'Filtered by selected range'
                                        : 'Default (±2 bulan dari sekarang)',
                                    font: { size: 12 },
                                    color: '#6b7280',
                                },
                                tooltip: {
                                    callbacks: {
                                        label(context) {
                                            const datasetLabel = context.dataset.label || '';
                                            const label = context.label || '';
                                            const value = context.raw;
                                            return `${datasetLabel} (${label}): ${value}`;
                                        },
                                    },
                                },
                            },
                        },
                    });
                };

                const fetchFinLossChart = () => {
                    if (!ctxFinLoss) return;
                    const url = buildUrlWithRange('/admin/dashboard/data/finloss', finlossDonutStart?.value, finlossDonutEnd?.value);
                    fetch(url)
                        .then((res) => res.json())
                        .then(renderFinLossChart)
                        .catch((err) => console.error('FinLoss chart error:', err));
                };

                finlossDonutApply?.addEventListener('click', fetchFinLossChart);
                finlossDonutReset?.addEventListener('click', () => {
                    if (finlossDonutStart) finlossDonutStart.value = '';
                    if (finlossDonutEnd) finlossDonutEnd.value = '';
                    fetchFinLossChart();
                });

                // ===== CASE 2: Stacked Bar Chart (Improvement) =====
                const ctxImprovement = document.getElementById('chartImprovement')?.getContext('2d');
                let improvementChartInstance = null;
                const improvementStart = document.getElementById('improvement-start');
                const improvementEnd = document.getElementById('improvement-end');
                const improvementApply = document.getElementById('improvement-apply');
                const improvementReset = document.getElementById('improvement-reset');

                const renderImprovementChart = (data) => {
                    if (!ctxImprovement) return;

                    if (improvementChartInstance) {
                        improvementChartInstance.destroy();
                        improvementChartInstance = null;
                    }

                    const datasets = [
                        {
                            label: 'High',
                            data: data.High ?? [],
                            backgroundColor: '#ef4444',
                            stack: 'Stack 0',
                        },
                        {
                            label: 'Medium',
                            data: data.Medium ?? [],
                            backgroundColor: '#f97316',
                            stack: 'Stack 0',
                        },
                        {
                            label: 'Low',
                            data: data.Low ?? [],
                            backgroundColor: '#22c55e',
                            stack: 'Stack 0',
                        },
                    ];

                    improvementChartInstance = new Chart(ctxImprovement, {
                        type: 'bar',
                        data: {
                            labels: data.labels ?? [],
                            datasets,
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: { mode: 'index', intersect: false },
                                title: {
                                    display: true,
                                    text: 'Improvement Findings by Priority',
                                },
                            },
                            scales: {
                                x: { stacked: true },
                                y: { stacked: true, beginAtZero: true },
                            },
                        },
                    });
                };

                const fetchImprovementChart = () => {
                    if (!ctxImprovement) return;
                    const url = buildUrlWithRange('/admin/dashboard/data/improvement', improvementStart?.value, improvementEnd?.value);
                    fetch(url)
                        .then((res) => res.json())
                        .then(renderImprovementChart)
                        .catch((err) => console.error('Improvement chart error:', err));
                };

                improvementApply?.addEventListener('click', fetchImprovementChart);
                improvementReset?.addEventListener('click', () => {
                    if (improvementStart) improvementStart.value = '';
                    if (improvementEnd) improvementEnd.value = '';
                    fetchImprovementChart();
                });

                // ===== CASE 3: Heatmap (Non-Compliance) =====
                const nonComplianceContainer = document.getElementById('chartNonCompliance');
                let nonComplianceChartInstance = null;
                const nonComplianceStart = document.getElementById('noncompliance-start');
                const nonComplianceEnd = document.getElementById('noncompliance-end');
                const nonComplianceApply = document.getElementById('noncompliance-apply');
                const nonComplianceReset = document.getElementById('noncompliance-reset');

                const renderNonComplianceChart = (payload) => {
                    if (!nonComplianceContainer) return;

                    const rawData = payload?.data ?? [];
                    const levels = ['High', 'Medium', 'Low'];
                    const seriesData = levels.map((level) => ({
                        name: level,
                        data: rawData.filter((item) => item.y === level).map((item) => ({ x: item.x, y: item.value })),
                    }));

                    if (nonComplianceChartInstance) {
                        nonComplianceChartInstance.destroy();
                        nonComplianceChartInstance = null;
                    }

                    nonComplianceContainer.innerHTML = '';

                    nonComplianceChartInstance = new ApexCharts(nonComplianceContainer, {
                        series: seriesData,
                        chart: { type: 'heatmap', height: '100%', toolbar: { show: false } },
                        colors: ['#ef4444', '#f97316', '#22c55e'],
                        plotOptions: { heatmap: { distributed: true, shadeIntensity: 0.5, useFillColorAsStroke: true } },
                        dataLabels: {
                            enabled: true,
                            style: { colors: ['#000'] },
                            formatter: (val) => (val === 0 ? '' : val),
                        },
                        yaxis: { categories: ['High', 'Medium', 'Low'] },
                        tooltip: { y: { formatter: (val) => `${val} cases` } },
                    });

                    nonComplianceChartInstance.render();
                };

                const fetchNonComplianceChart = () => {
                    if (!nonComplianceContainer) return;
                    const url = buildUrlWithRange('/admin/dashboard/data/noncompliance', nonComplianceStart?.value, nonComplianceEnd?.value);
                    fetch(url)
                        .then((res) => res.json())
                        .then(renderNonComplianceChart)
                        .catch((err) => console.error('Non-Compliance chart error:', err));
                };

                nonComplianceApply?.addEventListener('click', fetchNonComplianceChart);
                    nonComplianceReset?.addEventListener('click', () => {
                    if (nonComplianceStart) nonComplianceStart.value = '';
                    if (nonComplianceEnd) nonComplianceEnd.value = '';
                    fetchNonComplianceChart();
                });

                // Initial load for three primary charts
                fetchFinLossChart();
                fetchImprovementChart();
                fetchNonComplianceChart();

                // ===== CASE 4: Status Doughnut Chart =====
                const statusStartInput = document.getElementById('status-start-date');
                const statusEndInput = document.getElementById('status-end-date');
                const statusApplyBtn = document.getElementById('status-apply');
                const statusMessage = document.getElementById('status-chart-message');
                const statusCanvas = document.getElementById('chartStatusCircle');
                const statusCtx = statusCanvas?.getContext('2d');
                let statusChartInstance = null;

                const statusColors = ['#D2691E', '#7FFF00', '#FF7F50', '#8B4513'];

                const toggleStatusMessage = (show, text = 'No data for this period.') => {
                    if (!statusMessage) return;
                    statusMessage.textContent = text;
                    statusMessage.classList.toggle('hidden', !show);
                };

                const renderStatusChart = (labels = [], values = []) => {
                    if (!statusCtx) return;

                    if (statusChartInstance) {
                        statusChartInstance.destroy();
                        statusChartInstance = null;
                    }

                    const hasData = labels.length > 0 && values.some(value => Number(value ?? 0) > 0);
                    if (!hasData) {
                        toggleStatusMessage(true);
                        return;
                    }

                    toggleStatusMessage(false);

                    statusChartInstance = new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: labels.map((_, index) => statusColors[index % statusColors.length]),
                                borderColor: '#ffffff',
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 12 } } },
                            },
                        },
                    });
                };

                const buildStatusUrl = () => {
                    const url = new URL('/admin/dashboard/data/status', window.location.origin);
                    if (statusStartInput?.value) url.searchParams.append('start_date', statusStartInput.value);
                    if (statusEndInput?.value) url.searchParams.append('end_date', statusEndInput.value);
                    return url.toString();
                };

                const fetchStatusChart = () => {
                    if (!statusCtx) return;

                    fetch(buildStatusUrl())
                        .then(res => res.json())
                        .then(data => {
                            renderStatusChart(data.labels ?? [], data.values ?? []);
                        })
                        .catch(err => {
                            console.error('Status chart error:', err);
                            toggleStatusMessage(true, 'Error loading status data.');
                        });
                };

                statusApplyBtn?.addEventListener('click', fetchStatusChart);
                fetchStatusChart();

                // ===== CASE 5: Audit Engagement Doughnut Chart =====
                const engagementChartCanvas = document.getElementById('auditEngagementChart');
                const engagementCtx = engagementChartCanvas?.getContext('2d');
                const engagementMessage = document.getElementById('auditEngagementMessage');
                const engagementDetails = document.getElementById('auditEngagementDetails');
                const engagementYearSelect = document.getElementById('audit-engagement-year');
                let engagementChartInstance = null;

                const engagementColors = ['#FF7F50', '#D2691E', '#7FFF00', '#6495ED'];

                const toggleEngagementMessage = (show, text = 'No engagement data available.') => {
                    if (!engagementMessage) return;
                    engagementMessage.textContent = text;
                    engagementMessage.classList.toggle('hidden', !show);
                };

                const populateEngagementYears = (years = [], selectedYear = null) => {
                    if (!engagementYearSelect) return;
                    const currentValue = engagementYearSelect.value;
                    engagementYearSelect.innerHTML = '<option value="">All</option>';
                    years.forEach(year => {
                        const option = document.createElement('option');
                        option.value = year;
                        option.textContent = year;
                        if (selectedYear && Number(selectedYear) === Number(year)) option.selected = true;
                        engagementYearSelect.appendChild(option);
                    });
                    if (!selectedYear && currentValue && !engagementYearSelect.value) {
                        engagementYearSelect.value = currentValue;
                    }
                };

                const renderEngagementDetails = (items = []) => {
                    if (!engagementDetails) return;
                    engagementDetails.innerHTML = '';

                    if (!items.length) {
                        const emptyItem = document.createElement('li');
                        emptyItem.textContent = 'No engagement details to show.';
                        emptyItem.className = 'text-gray-400 dark:text-gray-500';
                        engagementDetails.appendChild(emptyItem);
                        return;
                    }

                    items.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-600';
                        li.innerHTML = `
                            <span class="font-medium">${item.title}</span>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">${item.count}</span>
                        `;
                        engagementDetails.appendChild(li);
                    });
                };

                const renderEngagementChart = (labels = [], values = []) => {
                    if (!engagementCtx) return;

                    if (engagementChartInstance) {
                        engagementChartInstance.destroy();
                        engagementChartInstance = null;
                    }

                    const hasData = labels.length > 0 && values.some(value => Number(value ?? 0) > 0);
                    if (!hasData) {
                        toggleEngagementMessage(true);
                        return;
                    }

                    toggleEngagementMessage(false);

                    engagementChartInstance = new Chart(engagementCtx, {
                        type: 'doughnut',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: labels.map((_, index) => engagementColors[index % engagementColors.length]),
                                borderColor: '#ffffff',
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 12 } } },
                            },
                        },
                    });
                };

                const buildEngagementUrl = () => {
                    const url = new URL('/admin/dashboard/data/report-titles', window.location.origin);
                    const selectedYear = engagementYearSelect?.value;
                    if (selectedYear) url.searchParams.append('year', selectedYear);
                    return url.toString();
                };

                const fetchEngagementData = () => {
                    if (!engagementCtx) return;

                    fetch(buildEngagementUrl())
                        .then(res => res.json())
                        .then(data => {
                            populateEngagementYears(data.years ?? [], data.selectedYear ?? null);
                            renderEngagementDetails(data.details ?? []);
                            renderEngagementChart(data.labels ?? [], data.values ?? []);
                        })
                        .catch(err => {
                            console.error('Audit engagement chart error:', err);
                            toggleEngagementMessage(true, 'Error loading engagement data.');
                            if (engagementDetails) {
                                engagementDetails.innerHTML = '<li class="text-red-500">Failed to load engagement details.</li>';
                            }
                        });
                };

                engagementYearSelect?.addEventListener('change', fetchEngagementData);
                fetchEngagementData();

                // ===== CASE 6: Fin Loss Summary & Findings =====
                const finlossSummaryCanvas = document.getElementById('finloss-summary-chart');
                const finlossFindingCanvas = document.getElementById('finloss-finding-chart');

                if (finlossSummaryCanvas || finlossFindingCanvas) {
                    const finlossSummaryCtx = finlossSummaryCanvas?.getContext('2d');
                    const finlossFindingCtx = finlossFindingCanvas?.getContext('2d');

                    const finlossCurrencyButtons = document.querySelectorAll('.finloss-currency-btn');
                    const finlossStartInput = document.getElementById('finloss-start-date');
                    const finlossEndInput = document.getElementById('finloss-end-date');
                    const finlossApplyBtn = document.getElementById('finloss-apply');
                    const finlossResetBtn = document.getElementById('finloss-reset');

                    const finlossSummaryCurrencyLabel = document.getElementById('finloss-summary-currency');
                    const finlossFindingCurrencyLabel = document.getElementById('finloss-finding-currency');
                    const finlossSummaryEmpty = document.getElementById('finloss-summary-empty');
                    const finlossFindingEmpty = document.getElementById('finloss-finding-empty');
                    const finlossSummaryInitialEl = document.getElementById('finloss-summary-initial');
                    const finlossSummaryRecoveryEl = document.getElementById('finloss-summary-recovery');
                    const finlossSummaryRemainingEl = document.getElementById('finloss-summary-remaining');

                    let finlossCurrentCurrency = 'idr';
                    let finlossSummaryChartInstance = null;
                    let finlossFindingChartInstance = null;
                    let finlossSummaryPayload = null;
                    let finlossFindingPayload = null;

                    const finlossCurrencyLabels = {
                        idr: 'Rupiah (IDR)',
                        usd: 'Dollar (USD)',
                    };

                    const finlossDatasetColors = {
                        'Initial Amount': { background: '#7FFF00', border: '#5fbf00' },
                        'Recovery': { background: '#FF7F50', border: '#d2691e' },
                        'Remaining': { background: '#6495ED', border: '#4169E1' },
                    };

                    const formatCurrency = (value) => {
                        if (finlossCurrentCurrency === 'usd') {
                            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value ?? 0);
                        }
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
                    };

                    const updateCurrencyLabels = () => {
                        const label = finlossCurrencyLabels[finlossCurrentCurrency] ?? '';
                        if (finlossSummaryCurrencyLabel) finlossSummaryCurrencyLabel.textContent = `Currency: ${label}`;
                        if (finlossFindingCurrencyLabel) finlossFindingCurrencyLabel.textContent = `Currency: ${label}`;
                    };

                    const toggleCurrencyButtons = () => {
                        finlossCurrencyButtons.forEach(button => {
                            if (!(button instanceof HTMLElement)) return;
                            const isActive = button.dataset.currency === finlossCurrentCurrency;
                            button.classList.toggle('bg-emerald-600', isActive);
                            button.classList.toggle('text-white', isActive);
                            button.classList.toggle('bg-white', !isActive);
                            button.classList.toggle('dark:bg-gray-800', !isActive);
                            button.classList.toggle('text-emerald-600', !isActive);
                            button.classList.toggle('dark:text-emerald-300', !isActive);
                        });
                    };

                    const extractSummaryTotals = () => {
                        if (!finlossSummaryPayload?.totals) {
                            return null;
                        }
                        if (finlossCurrentCurrency === 'usd') {
                            return {
                                initial: Number(finlossSummaryPayload.totals.agreed_usd ?? 0),
                                recovery: Number(finlossSummaryPayload.totals.recovery_usd ?? 0),
                                remaining: Number(finlossSummaryPayload.totals.remaining_usd ?? 0),
                            };
                        }
                        return {
                            initial: Number(finlossSummaryPayload.totals.agreed_idr ?? 0),
                            recovery: Number(finlossSummaryPayload.totals.recovery_idr ?? 0),
                            remaining: Number(finlossSummaryPayload.totals.remaining_idr ?? 0),
                        };
                    };

                    const renderFinlossSummary = () => {
                        if (!finlossSummaryCtx) return;

                        if (finlossSummaryChartInstance) {
                            finlossSummaryChartInstance.destroy();
                            finlossSummaryChartInstance = null;
                        }

                        const totals = extractSummaryTotals();
                        const values = totals ? [totals.initial, totals.recovery, totals.remaining] : [];
                        const hasData = totals && values.some(value => Number(value ?? 0) > 0);

                        updateCurrencyLabels();

                        if (!hasData) {
                            if (finlossSummaryEmpty) finlossSummaryEmpty.classList.remove('hidden');
                            if (finlossSummaryInitialEl) finlossSummaryInitialEl.textContent = '-';
                            if (finlossSummaryRecoveryEl) finlossSummaryRecoveryEl.textContent = '-';
                            if (finlossSummaryRemainingEl) finlossSummaryRemainingEl.textContent = '-';
                            return;
                        }

                        if (finlossSummaryEmpty) finlossSummaryEmpty.classList.add('hidden');

                        if (finlossSummaryInitialEl) finlossSummaryInitialEl.textContent = formatCurrency(totals.initial);
                        if (finlossSummaryRecoveryEl) finlossSummaryRecoveryEl.textContent = formatCurrency(totals.recovery);
                        if (finlossSummaryRemainingEl) finlossSummaryRemainingEl.textContent = formatCurrency(totals.remaining);

                        finlossSummaryChartInstance = new Chart(finlossSummaryCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Initial Amount', 'Recovery', 'Remaining'],
                                datasets: [{
                                    data: values,
                                    backgroundColor: ['#7FFF00', '#FF7F50', '#6495ED'],
                                    borderColor: '#ffffff',
                                    borderWidth: 2,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '62%',
                                plugins: {
                                    legend: { position: 'bottom', labels: { font: { size: 12 } } },
                                    tooltip: {
                                        callbacks: {
                                            label: context => {
                                                const label = context.label ? `${context.label}: ` : '';
                                                return `${label}${formatCurrency(context.raw)}`;
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    };

                    const renderFinlossFinding = () => {
                        if (!finlossFindingCtx) return;

                        if (finlossFindingChartInstance) {
                            finlossFindingChartInstance.destroy();
                            finlossFindingChartInstance = null;
                        }

                        const block = finlossFindingPayload ? finlossFindingPayload[finlossCurrentCurrency] : null;
                        const labels = Array.isArray(block?.labels) ? block.labels : [];
                        const rawDatasets = Array.isArray(block?.datasets) ? block.datasets : [];

                        const datasets = rawDatasets.map(dataset => {
                            const colors = finlossDatasetColors[dataset.label] ?? { background: '#CBD5F5', border: '#94A3B8' };
                            return {
                                label: dataset.label,
                                data: Array.isArray(dataset.data) ? dataset.data : [],
                                backgroundColor: colors.background,
                                borderColor: colors.border,
                                borderWidth: 1,
                                maxBarThickness: 42,
                            };
                        });

                        const hasData = labels.length > 0 && datasets.some(ds => ds.data.some(value => Number(value ?? 0) > 0));

                        updateCurrencyLabels();

                        if (!hasData) {
                            if (finlossFindingEmpty) finlossFindingEmpty.classList.remove('hidden');
                            return;
                        }

                        if (finlossFindingEmpty) finlossFindingEmpty.classList.add('hidden');

                        finlossFindingChartInstance = new Chart(finlossFindingCtx, {
                            type: 'bar',
                            data: { labels, datasets },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { mode: 'index', intersect: false },
                                scales: {
                                    x: { stacked: false },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: value => formatCurrency(value),
                                        },
                                    },
                                },
                                plugins: {
                                    legend: { position: 'top' },
                                    tooltip: {
                                        callbacks: {
                                            label: context => {
                                                const label = context.dataset?.label ? `${context.dataset.label}: ` : '';
                                                return `${label}${formatCurrency(context.parsed.y)}`;
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    };

                    const buildSummaryUrl = () => {
                        const url = new URL('/admin/dashboard/data/finloss-global', window.location.origin);
                        if (finlossStartInput?.value) url.searchParams.append('start_date', finlossStartInput.value);
                        if (finlossEndInput?.value) url.searchParams.append('end_date', finlossEndInput.value);
                        return url.toString();
                    };

                    const buildFindingUrl = () => {
                        const url = new URL('/admin/dashboard/data/audit-recap', window.location.origin);
                        if (finlossStartInput?.value) url.searchParams.append('start_date', finlossStartInput.value);
                        if (finlossEndInput?.value) url.searchParams.append('end_date', finlossEndInput.value);
                        return url.toString();
                    };

                    const fetchFinlossSummary = () => {
                        if (!finlossSummaryCtx) return;
                        fetch(buildSummaryUrl())
                            .then(res => res.json())
                            .then(data => {
                                finlossSummaryPayload = data;
                                renderFinlossSummary();
                            })
                            .catch(err => {
                                console.error('Fin loss summary error:', err);
                                finlossSummaryPayload = null;
                                if (finlossSummaryEmpty) {
                                    finlossSummaryEmpty.textContent = 'Error loading summary data.';
                                    finlossSummaryEmpty.classList.remove('hidden');
                                }
                                if (finlossSummaryInitialEl) finlossSummaryInitialEl.textContent = '-';
                                if (finlossSummaryRecoveryEl) finlossSummaryRecoveryEl.textContent = '-';
                                if (finlossSummaryRemainingEl) finlossSummaryRemainingEl.textContent = '-';
                            });
                    };

                    const fetchFinlossFindings = () => {
                        if (!finlossFindingCtx) return;
                        fetch(buildFindingUrl())
                            .then(res => res.json())
                            .then(data => {
                                finlossFindingPayload = data;
                                renderFinlossFinding();
                            })
                            .catch(err => {
                                console.error('Fin loss finding error:', err);
                                finlossFindingPayload = null;
                                if (finlossFindingEmpty) {
                                    finlossFindingEmpty.textContent = 'Error loading finding data.';
                                    finlossFindingEmpty.classList.remove('hidden');
                                }
                            });
                    };

                    finlossCurrencyButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            const nextCurrency = button.dataset.currency;
                            if (!nextCurrency || nextCurrency === finlossCurrentCurrency) return;
                            finlossCurrentCurrency = nextCurrency;
                            toggleCurrencyButtons();
                            renderFinlossSummary();
                            renderFinlossFinding();
                        });
                    });

                    finlossApplyBtn?.addEventListener('click', () => {
                        fetchFinlossSummary();
                        fetchFinlossFindings();
                    });

                    finlossResetBtn?.addEventListener('click', () => {
                        if (finlossStartInput) finlossStartInput.value = '';
                        if (finlossEndInput) finlossEndInput.value = '';
                        fetchFinlossSummary();
                        fetchFinlossFindings();
                    });

                    toggleCurrencyButtons();
                    updateCurrencyLabels();
                    fetchFinlossSummary();
                    fetchFinlossFindings();
                }
            });
            </script>
            @endpush

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">Fin Loss Overview</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Monitor total initial amounts, recoveries, and remaining balance per finding.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex rounded-full border border-emerald-200 dark:border-emerald-500 overflow-hidden">
                            <button type="button" data-currency="idr" class="finloss-currency-btn px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white">Rupiah (IDR)</button>
                            <button type="button" data-currency="usd" class="finloss-currency-btn px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-300">Dollar (USD)</button>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <input type="date" id="finloss-start-date" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            <span class="text-gray-500 dark:text-gray-400">-</span>
                            <input type="date" id="finloss-end-date" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <button id="finloss-apply" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition">Apply</button>
                        <button id="finloss-reset" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg transition">Reset</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Overall Total</h3>
                                <p id="finloss-summary-currency" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currency: Rupiah (IDR)</p>
                            </div>
                        </div>
                        <div class="relative h-64 mt-4">
                            <canvas id="finloss-summary-chart"></canvas>
                            <p id="finloss-summary-empty" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400 px-6">No data for this period and currency.</p>
                        </div>
                        <div class="mt-5 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color:#7FFF00"></span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">Initial Amount</span>
                                </div>
                                <span id="finloss-summary-initial" class="font-semibold text-gray-900 dark:text-gray-100">-</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color:#FF7F50"></span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">Recovery</span>
                                </div>
                                <span id="finloss-summary-recovery" class="font-semibold text-gray-900 dark:text-gray-100">-</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color:#6495ED"></span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">Remaining</span>
                                </div>
                                <span id="finloss-summary-remaining" class="font-semibold text-gray-900 dark:text-gray-100">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Fin Loss per Engagement</h3>
                                <p id="finloss-finding-currency" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currency: Rupiah (IDR)</p>
                            </div>
                        </div>
                        <div class="relative h-72 mt-4">
                            <canvas id="finloss-finding-chart"></canvas>
                            <p id="finloss-finding-empty" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400 px-6">No engagement data for this period and currency.</p>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>