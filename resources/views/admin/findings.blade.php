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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 space-y-4 lg:space-y-0">
    
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <button class="px-4 py-2 text-sm font-semibold text-red-800 dark:text-red-300 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center space-x-2">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        <span>Open</span>
                    </button>
                    <button class="px-4 py-2 text-sm font-semibold text-yellow-800 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center space-x-2">
                        <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                        <span>On Progress</span>
                    </button>
                    <button class="px-4 py-2 text-sm font-semibold text-green-800 dark:text-green-300 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center space-x-2">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        <span>Close</span>
                    </button>
                    <button class="px-4 py-2 text-sm font-semibold text-orange-800 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/50 rounded-full flex items-center space-x-2">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        <span>Overdue</span>
                    </button>
            </div>

            <div class="relative w-full lg:ml-4">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-700/50 rounded-full focus:outline-none focus:ring-2 focus:ring-green-400 border-none">
            </div>
        </div>
                    <br>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Finding ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Auditor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Auditee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Financial Loss</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">#NC-001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Unauthorized Access to Server Room</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Open</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">High</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">John Doe</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">IT Department</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Non Compliance</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">-</td>
                                </tr>

                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">#FL-001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Inventory Discrepancy</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">On Progress</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-600 dark:text-yellow-400">Medium</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Jane Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Warehouse</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Find Loss</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-500">Rp 80.000,-</td>
                                </tr>

                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">#IM-001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Outdated Backup Procedure</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Closed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">Low</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Robert Brown</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">IT Department</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Improvement</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">-</td>
                                </tr>

                                 <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">#NC-002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Missing Fire Extinguisher</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300">Overdue</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">High</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Emily White</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Facility Mgmt</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Non Compliance</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">-</td>
                                </tr>

                                </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>