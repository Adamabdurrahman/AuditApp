<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Create Audit Finding
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Fill out the form below to create a new audit finding.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        Create Audit Finding
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 border-b dark:border-gray-700 pb-3 mb-4">
                        Fill out the form below to create a new audit finding.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-md">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('admin.findings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf 
                    <div class="space-y-6">

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Information Details</h3>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Judul Temuan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Masukkan judul temuan audit"
                            >
                        </div>

                        <div>
                            <label for="department_pic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Departemen / PIC
                            </label>
                            <input 
                                type="text" 
                                id="department_pic" 
                                name="department_pic" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Contoh: IT Department, Budi Santoso"
                            >
                        </div>

                        <div>
                            <label for="department_pic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Auditor
                            </label>
                                <input 
                                    type="text" 
                                    id="auditor" 
                                    name="auditor" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Contoh: John Doe"
                                    value="{{ auth()->user()->name }}" 
                                    readonly
                                >
                                <input type="hidden" name="auditor" value="{{ auth()->id() }}">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temuan Audit (Finding Description)</label>
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Describe the audit finding in detail"></textarea>
                        </div>

                        <!-- Bungkus semua dalam satu x-data -->
                        <div 
                            x-data="{
                                selectedCategory: 'Fin Loss',
                                selectedPriority: 'High',
                                subCategory: 'Recovery',
                                items: [{ description: '', value: '' }],
                                total: 0,
                                exchangeRate: {{ $exchangeRate }},
                                get totalUsd() {
                                    return this.total > 0 
                                        ? (this.total / this.exchangeRate).toFixed(2) 
                                        : '0.00';
                                },
                                startDate: '', // ← Sekarang user yang isi
                                findingDate: new Date().toISOString().split('T')[0], 
                                dueDate: '',
                                client: {
                                    pt: '',
                                    name: '',
                                    email: ''
                                },
                                reminder: {
                                    name: '',
                                    email: ''
                                },
                                calculateTotal() {
                                    this.total = this.items.reduce((acc, item) => acc + (parseFloat(item.value) || 0), 0);
                                }
                            }"
                            x-init="calculateTotal()"
                            class="space-y-6"
                        >

                            <!-- Kategori & Priority -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori (Category)</label>
                                    <select 
                                        id="category" 
                                        name="category"
                                        x-model="selectedCategory"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    >
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                        
                                    </select>

                                    {{-- <input type="hidden" name="category" :value="selectedCategory"> --}}
                                </div>

                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select 
                                        id="priority" 
                                        name="priority"
                                        x-model="selectedPriority"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    >
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->name }}">{{ $priority->name }}</option>
                                        @endforeach
                                        
                                    </select>

                                    {{-- <input type="hidden" name="priority" :value="selectedPriority"> --}}
                                </div>

                                <!-- Sub Kategori: hanya untuk Fin Loss -->
                                <div 
                                    x-show="selectedCategory === 'Fin Loss'"
                                    x-transition
                                    class="md:col-span-2"
                                >
                                    <label for="sub_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sub Kategori
                                    </label>
                                    <select 
                                        id="sub_category" 
                                        name="sub_category"
                                        x-model="subCategory"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    >
                                        @foreach($subcategories as $sub)
                                            <option value="{{ $sub->name }}">{{ $sub->name }}</option>
                                        @endforeach
                                        
                                    </select>

                                    {{-- <input type="hidden" name="sub_category" :value="subCategory"> --}}
                                </div>
                            </div>

                            <!-- Fin Loss Details: hanya muncul jika Fin Loss -->
                            <div 
                                x-show="selectedCategory === 'Fin Loss'"
                                x-transition
                                x-cloak
                            >
                                <div class="space-y-4 pt-4 border-t dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fin Loss Details</h3>
                                    
                                    <div class="space-y-4">
                                        <template x-for="(item, index) in items" :key="index">
                                            <div class="flex items-end space-x-3 border dark:border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex-grow grid grid-cols-3 gap-x-4">
                                                    <div class="col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi kerugian (e.g., Pasir, Api)</label>
                                                        <input 
                                                            x-model="item.description" 
                                                            @input="calculateTotal" 
                                                            name="loss_description[]" 
                                                            type="text" 
                                                            placeholder="e.g. Pasir" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                                        >
                                                    </div>
                                                    <div class="col-span-1">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nilai kerugian (Rp)</label>
                                                        <div class="relative mt-1">
                                                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 sm:text-sm">Rp</span>
                                                            <input 
                                                                x-model="item.value" 
                                                                @input="calculateTotal" 
                                                                name="loss_value[]" 
                                                                type="number" 
                                                                placeholder="Enter value" 
                                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm pl-8"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                <button 
                                                    @click.prevent="items.splice(index, 1); calculateTotal()" 
                                                    x-show="items.length > 1 || (items.length === 1 && (item.description || item.value))"
                                                    class="text-gray-400 hover:text-red-500 mb-1"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <button 
                                        @click.prevent="items.push({ description: '', value: '' })" 
                                        class="flex items-center space-x-2 text-sm font-medium text-green-600 hover:text-green-800"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Add Loss Item</span>
                                    </button>

                                    <div class="flex justify-between items-center text-sm text-gray-800 dark:text-gray-100 pt-3 border-t dark:border-gray-700">
                                        <span>Total Nilai Kerugian (IDR)</span>
                                        <span class="ml-4 font-bold">Rp <span x-text="total.toLocaleString('id-ID')">0</span></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-800 dark:text-gray-100 pt-2">
                                        <span>Total Nilai Kerugian (USD)</span>
                                        <span class="ml-4 font-bold"> $ <span x-text="totalUsd">0.00</span></span>
                                    </div>
                                </div>
                            </div>


                            <!-- Timeline Section -->
                            <div class="pt-6 border-t dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Timeline</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Tanggal Temuan (Otomatis = Hari Ini) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Tanggal Temuan
                                        </label>
                                        <input 
                                            type="text" 
                                            :value="findingDate"
                                            readonly
                                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 shadow-sm cursor-not-allowed"
                                        >
                                        <input type="hidden" name="finding_date" :value="findingDate">
                                    </div>

                                    <!-- Start Date (Manual) -->
                                    <div>
                                        <label for="start_date_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Start Date <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="start_date_input"
                                            x-model="startDate"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        >
                                        <input type="hidden" name="start_date" :value="startDate">
                                    </div>

                                    <!-- Due Date (Manual) -->
                                    <div>
                                        <label for="due_date_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            End Date <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="due_date_input"
                                            x-model="dueDate"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        >
                                        <input type="hidden" name="due_date" :value="dueDate">
                                    </div>
                                </div>
                            </div>
          
                            <!-- Pihak Ketiga / Reminder Section -->
                            <div class="pt-6 border-t dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pihak Auditee</h3>

                                <!-- Client Section (hanya jika Fin Loss) -->
                                <div 
                                    x-show="selectedCategory === 'Fin Loss'"
                                    x-transition
                                    class="space-y-4"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label for="client_pt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                PT <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="client_pt" 
                                                name="client_pt"
                                                x-model="client.pt"
                                                :required="selectedCategory === 'Fin Loss'"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="Contoh: PT ABC Indonesia"
                                            >
                                            {{-- <input type="hidden" name="client_pt" :value="client.pt"> --}}
                                        </div>
                                        <div>
                                            <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nama <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="client_name" 
                                                name="client_name"
                                                x-model="client.name"
                                                :required="selectedCategory === 'Fin Loss'"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="Contoh: Budi Santoso"
                                            >
                                            {{-- <input type="hidden" name="client_name" :value="client.name"> --}}
                                        </div>
                                        <div>
                                            <label for="client_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="email" 
                                                id="client_email" 
                                                name="client_email"
                                                x-model="client.email"
                                                :required="selectedCategory === 'Fin Loss'"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="budi@abc.com"
                                            >
                                            {{-- <input type="hidden" name="client_email" :value="client.email"> --}}
                                        </div>
                                    </div>
                                </div>

                                <!-- Reminder Section (jika bukan Fin Loss) -->
                                <div 
                                    x-show="selectedCategory !== 'Fin Loss'"
                                    x-transition
                                    class="space-y-4"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="reminder_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nama <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="reminder_name" 
                                                name="reminder_name"
                                                x-model="reminder.name"
                                                :required="selectedCategory !== 'Fin Loss'"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="Contoh: Andi Pratama"
                                            >
                                            {{-- <input type="hidden" name="reminder_name" :value="reminder.name"> --}}
                                        </div>
                                        <div>
                                            <label for="reminder_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="email" 
                                                id="reminder_email" 
                                                name="reminder_email"
                                                x-model="reminder.email"
                                                :required="selectedCategory !== 'Fin Loss'"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                placeholder="andi@perusahaan.com"
                                            >
                                            {{-- <input type="hidden" name="reminder_email" :value="reminder.email"> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t dark:border-gray-700 space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Evidence & Catatan</h3>

                             <div>
                                <label for="internal_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rekomendasi Auditor</label>
                                <textarea id="internal_notes" name="internal_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Add internal notes for the audit team..."></textarea>
                            </div>
                            <div>
                                <label for="auditee_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Active Respond</label>
                                <textarea id="auditee_notes" name="auditee_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Initial message or context for the auditee..."></textarea>
                            </div>
                        </div>

                        <!-- File Attachments dengan Preview + Remove + Kompatibilitas Penuh -->
                        <div 
                            x-data="{
                                fileName: '',
                                dragActive: false,
                                handleFileChange(event) {
                                    const files = event.target.files;
                                    console.log('Selected files:', files);
                                    this.fileName = files.length > 0 ? Array.from(files).map(f => f.name).join(', ') : '';
                                },
                                removeFile() {
                                    // Dapatkan elemen input file
                                    const input = document.getElementById('file_upload');
                                    // Reset nilainya
                                    input.value = '';
                                    // Trigger event 'change' agar Alpine tahu
                                    input.dispatchEvent(new Event('change'));
                                }
                            }"
                            class="pt-4 border-t dark:border-gray-700"
                        >
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                File Attachments
                            </label>

                            <div 
                                @dragover.prevent @drop.prevent
                                @drop="
                                    const input = document.getElementById('file_upload');
                                    input.files = $event.dataTransfer.files;
                                    input.dispatchEvent(new Event('change'));
                                "
                                
                                class="mt-1 flex justify-center rounded-md border-2 border-dashed px-6 pt-5 pb-6 transition-colors duration-200"
                            >
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                    <!-- Preview nama file -->
                                    <template x-if="fileName">
                                        <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="fileName"></span>
                                            <button 
                                                type="button" 
                                                @click="removeFile"
                                                class="ml-2 text-red-500 hover:text-red-700 text-xs"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Instruksi upload -->
                                    <div x-show="!fileName">
                                        <div class="flex flex-col">
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label 
                                                    for="file_upload" 
                                                    class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-medium text-green-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 hover:text-green-500"
                                                >
                                                    <span>Click to upload</span>
                                                    <input 
                                                        id="file_upload" 
                                                        name="file_upload[]" 
                                                        type="file" 
                                                        multiple
                                                        class="sr-only" 
                                                        @change="handleFileChange"
                                                        accept=".png,.jpg,.jpeg,.pdf"
                                                    >
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                                PNG, JPG, PDF (MAX. 5MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="pt-6 flex justify-end space-x-3">

                        <a href="{{ route('admin.findings') }}" 
                        class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-300">
                            Cancel
                        </a>

                        <button type="submit" class="bg-green-500 text-black font-bold py-2 px-5 rounded-lg shadow-md hover:bg-green-600 transition duration-300">
                            Submit
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</x-app-layout>