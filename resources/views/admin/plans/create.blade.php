<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <a href="{{ route('admin.plans.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">Audit Plans</a>
            <svg class="mx-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-semibold text-gray-700 dark:text-gray-100">Buat Plan</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-xl p-6">
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-1">Plan Audit Baru</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Plan akan menjadi induk dari beberapa temuan (findings) pada tahun tertentu.</p>

                <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Plan <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="Contoh: Business Trip">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" id="year" name="year" value="{{ old('year', $defaultYear) }}" min="2000" max="2999" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('year')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'planned') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Catatan atau ruang lingkup plan...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Batal</a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow transition">Simpan Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
