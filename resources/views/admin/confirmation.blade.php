<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">

                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        Verifikasi Audit Final
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Tinjau detail temuan secara menyeluruh sebelum menutup audit.
                    </p>
                </div>

                <div class="border dark:border-gray-700 rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Temuan</h2>

                    <hr class="dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <!-- ID Temuan -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">ID Temuan</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                #{{ $finding->id }}
                            </p>
                        </div>

                        <!-- Nama Audit -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Nama Audit</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $finding->judul_temuan ?? '—' }}
                            </p>
                        </div>

                        <!-- Auditee -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Auditee</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $finding->reminder->email ?? '—' }}
                            </p>
                        </div>


                        <!-- Status -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Status</p>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if(isset($finding->status->status) && strtolower($finding->status->status) === 'open')
                                    bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                                @elseif(isset($finding->status->status) && strtolower($finding->status->status) === 'closed')
                                    bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                @else
                                    bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                @endif">
                                {{ $finding->status->status ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <hr class="dark:border-gray-700">

                    <div class="space-y-4 text-sm">
                        <!-- Temuan Audit -->
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Finding Description</p>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                {{ $finding->temuan_audit ?? '—' }}
                            </p>
                        </div>

                        <!-- Rekomendasi Author -->
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Rekomendasi Author</p>
                            <p class="mt-1 italic text-gray-600 dark:text-gray-400">
                                "{{ $finding->rekomendasi_author ?? '—' }}"
                            </p>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Catatan Tambahan</p>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                {{ $finding->catatan_tambahan ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.findings') }}"
                        class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-300">
                        Cancel
                    </a>
                    <form action="{{ route('admin.findings.close', $finding->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-500 text-white font-bold py-2 px-5 rounded-lg shadow-md hover:bg-green-600 transition duration-300">
                            Approve & Close
                        </button>
                    </form>
                </div> 
                
            </div>
        </div>
    </div>
</x-app-layout>
