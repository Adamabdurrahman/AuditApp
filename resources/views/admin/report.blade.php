<head>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Audit Report</h1>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button class="flex items-center space-x-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm px-3 py-1.5 text-sm"><span>Category</span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                    <button class="flex items-center space-x-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm px-3 py-1.5 text-sm"><span>Status</span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                    <button class="flex items-center space-x-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm px-3 py-1.5 text-sm"><span>Priority</span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                    <button class="flex items-center space-x-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm px-3 py-1.5 text-sm"><span>Auditee</span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                    <button class="flex items-center space-x-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm px-3 py-1.5 text-sm"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span>Date Range</span></button>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Findings Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 flex flex-col justify-center items-center">
                        <h3 class="font-semibold self-start dark:text-gray-100">Findings by Kategori</h3>
                        <div id="pie-chart-kategori" class="w-full"></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 flex flex-col justify-center items-center">
                        <h3 class="font-semibold self-start dark:text-gray-100">Findings by Status</h3>
                        <div id="radial-chart-status" class="w-full"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Financial Loss Analysis</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 space-y-4">
                        <h3 class="font-semibold dark:text-gray-100">Total Loss by Item</h3>
                        <div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">Rp 150,000,000</p>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                <span class="text-green-600">+10%</span> from last period
                            </p>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div>
                                <div class="flex justify-between mb-1 text-gray-600 dark:text-gray-300"><span>Asphalt</span><span>50%</span></div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="bg-gray-400 h-2 rounded-full" style="width: 50%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1 text-gray-600 dark:text-gray-300"><span>Sand</span><span>90%</span></div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="bg-gray-400 h-2 rounded-full" style="width: 90%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1 text-gray-600 dark:text-gray-300"><span>Cement</span><span>50%</span></div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="bg-gray-400 h-2 rounded-full" style="width: 50%"></div></div>
                            </div>
                             <div>
                                <div class="flex justify-between mb-1 text-gray-600 dark:text-gray-300"><span>Steel</span><span>40%</span></div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="bg-gray-400 h-2 rounded-full" style="width: 40%"></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                         <div>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">Rp 200,000,000</p>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                <span class="text-green-600">+5%</span> in 2024
                            </p>
                        </div>
                        <div id="bar-chart-loss-time"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Opsi untuk Pie Chart Kategori
        var pieOptions = {
            series: [44, 55, 13],
            chart: {
                type: 'pie',
                height: 250
            },
            labels: ['Find Loss', 'Non Compliance', 'Improvement'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    }
                }
            }]
        };
        var pieChart = new ApexCharts(document.querySelector("#pie-chart-kategori"), pieOptions);
        pieChart.render();

        // Opsi untuk Semi-Donut Chart Status
        var radialOptions = {
            series: [76, 67],
            chart: {
                height: 250,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: {
                        size: '60%',
                    },
                    track: {
                        background: '#e7e7e7',
                        strokeWidth: '97%',
                    },
                    dataLabels: {
                        name: { show: false },
                        value: { show: false }
                    }
                }
            },
            labels: ['Open', 'Closed'],
        };
        var radialChart = new ApexCharts(document.querySelector("#radial-chart-status"), radialOptions);
        radialChart.render();

        // Opsi untuk Bar Chart Loss Over Time
        var barOptions = {
            series: [{
                name: 'Total Loss',
                data: [20, 31, 40, 28, 51, 42, 109, 100, 90, 60, 45, 22] // Data dalam Juta
            }],
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '50%',
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            grid: {
                show: false
            }
        };
        var barChart = new ApexCharts(document.querySelector("#bar-chart-loss-time"), barOptions);
        barChart.render();
    });
</script>