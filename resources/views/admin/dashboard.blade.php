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
            <button class="w-full md:w-auto mt-4 md:mt-0 bg-green-500 text-white font-bold py-2 px-5 rounded-3xl shadow-md hover:bg-green-600 transition duration-300">
                + Create New Audit Finding
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Total Findings</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2">120</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Outstanding Findings</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2">35</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Overdue</p>
                    <p class="text-4xl font-bold text-red-500 dark:text-red-400 mt-2">7</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/50 p-6 rounded-xl shadow">
                    <p class="text-sm text-green-800 dark:text-green-300">Notifications</p>
                    <p class="text-4xl font-bold text-gray-800 dark:text-gray-100 mt-2">3</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-300">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Findings by Kategori</h2>
                    <div class="flex space-x-4">
                        <div class="flex-1 text-center bg-green-100 dark:bg-green-900/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Non Compliance</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">15</p>
                        </div>
                        <div class="flex-1 text-center bg-green-100 dark:bg-green-900/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Improvement</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">12</p>
                        </div>
                        <div class="flex-1 text-center bg-green-100 dark:bg-green-900/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Find Loss</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">8</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-300">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Findings by Priority</h2>
                    <div class="flex space-x-4">
                        <div class="flex-1 text-center bg-red-100 dark:bg-red-900/50 p-4 rounded-lg">
                            <p class="text-sm text-red-700 dark:text-red-300">High</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">7</p>
                        </div>
                        <div class="flex-1 text-center bg-yellow-100 dark:bg-yellow-900/50 p-4 rounded-lg">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">Medium</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">20</p>
                        </div>
                        <div class="flex-1 text-center bg-green-100 dark:bg-green-900/50 p-4 rounded-lg">
                            <p class="text-sm text-green-700 dark:text-green-300">Low</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">10</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-300">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Recent Notifications</h2>
                    <a href="#" class="bg-green-500 text-white font-semibold py-2 px-4 rounded-3xl flex items-center space-x-2 hover:bg-green-600 transition duration-300">
                        <span>View All</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You have <span class="font-bold text-gray-800 dark:text-gray-200">3 unread notifications.</span></p>

                <div class="space-y-4">
                    <div class="flex items-start space-x-4 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-gray-900 dark:text-gray-100">Explanation submitted for Finding #NC-001</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Auditee: John Doe <span class="ml-2"><br>2 hours ago</span></p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 p-4">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-gray-900 dark:text-gray-100">Extend date request for Finding #FL-003</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Auditee: Adam Abdurrahman <span class="ml-2"><br>2 hours ago</span></p>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>