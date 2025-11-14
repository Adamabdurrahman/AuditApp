<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Audit Findings</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl">
                    All findings are grouped by <strong>Audit Engagement</strong>. Select or create an engagement title to review its findings.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition">
                    + New Engagement Title
                </a>
                <a href="{{ route('admin.findings.create') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-green-500 text-white font-semibold hover:bg-green-600 transition">
                    + New Finding
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6">
                <form method="GET" action="{{ route('admin.findings') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Year</label>
                        <select name="year" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected(($filters['year'] ?? '') == $year)>Year {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search Findings</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search engagement title or finding..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>
                    <div class="md:col-span-3 flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700">Apply Filters</button>
                        <a href="{{ route('admin.findings') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse($plans as $plan)
                    @php
                        $displayCount = $hasFindingFilters ? $plan->filtered_findings_count : $plan->findings_count;
                    @endphp
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Year {{ $plan->year ?? '-' }}</p>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $plan->title }}</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-3">{{ $plan->description ?? 'No description provided yet.' }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                {{ $displayCount }} Findings
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('admin.plans.show', $plan) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">View Details</a>
                            <a href="{{ route('admin.findings.create', ['plan' => $plan->id]) }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-emerald-500 text-emerald-600 text-xs font-semibold hover:bg-emerald-50">Add Finding</a>
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-blue-500 text-blue-600 text-xs font-semibold hover:bg-blue-50">Edit Title</a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Delete engagement title and detach all related findings?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg border border-red-500 text-red-600 text-xs font-semibold hover:bg-red-50">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">No data matches the current filters</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Adjust the filters or create a new engagement title to start adding findings.</p>
                            <a href="{{ route('admin.plans.create') }}" class="mt-5 inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Create Engagement Title</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</x-app-layout>
