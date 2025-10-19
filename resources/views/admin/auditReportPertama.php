<head>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Audit Report</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Graphical reports for audit findings, focusing on Find Loss and its items.</p>
                </div>
                <div class="flex items-center space-x-2 mt-4 md:mt-0 w-full md:w-auto">
                    <select class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option>Find Loss</option>
                        <option>Non Compliance</option>
                    </select>
                    <input type="date" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <button class="flex-shrink-0 bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-green-600 transition duration-300 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        <span>Export Report</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Financial Loss by Item</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Distribution for 'Find Loss' Category</p>
                    <div id="donut-chart"></div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center"><span class="h-3 w-3 rounded-full bg-green-200 mr-2"></span>Asphalt</span>
                            <span class="font-semibold dark:text-gray-200">Rp166Jt</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="flex items-center"><span class="h-3 w-3 rounded-full bg-green-300 mr-2"></span>Sand</span>
                            <span class="font-semibold dark:text-gray-200">Rp155Jt</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="flex items-center"><span class="h-3 w-3 rounded-full bg-green-400 mr-2"></span>Gravel</span>
                            <span class="font-semibold dark:text-gray-200">Rp124Jt</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="flex items-center"><span class="h-3 w-3 rounded-full bg-green-500 mr-2"></span>Cement</span>
                            <span class="font-semibold dark:text-gray-200">Rp93Jt</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="flex items-center"><span class="h-3 w-3 rounded-full bg-green-600 mr-2"></span>Others</span>
                            <span class="font-semibold dark:text-gray-200">Rp62Jt</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Loss Item Breakdown (Rp)</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Monthly loss per item for the last year</p>
                        </div>
                        <select class="w-40 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option>Sand</option>
                            <option>Asphalt</option>
                            <option>Gravel</option>
                        </select>
                    </div>
                    <div id="bar-chart"></div>
                </div>
            </div>
        </div>
    </div>

    </x-app-layout>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Opsi untuk Donut Chart
        var donutOptions = {
            series: [166, 155, 124, 93, 62],
            chart: {
                type: 'donut',
                height: 300,
            },
            labels: ['Asphalt', 'Sand', 'Gravel', 'Cement', 'Others'],
            colors: ['#A7F3D0', '#6EE7B7', '#34D399', '#10B981', '#059669'],
            legend: {
                show: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Loss',
                                fontSize: '16px',
                                color: '#6B7280',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return 'Rp' + total + 'Jt';
                                }
                            }
                        }
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var donutChart = new ApexCharts(document.querySelector("#donut-chart"), donutOptions);
        donutChart.render();

        // Opsi untuk Bar Chart (dengan data contoh)
        var barOptions = {
            series: [{
                name: 'Loss',
                data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2]
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            colors: ['#10B981'],
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
                title: {
                    text: '(dalam Juta Rupiah)'
                }
            },
        };

        var barChart = new ApexCharts(document.querySelector("#bar-chart"), barOptions);
        barChart.render();
    });
</script>