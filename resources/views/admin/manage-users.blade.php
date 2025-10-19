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
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">ID Temuan</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">#12345</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Nama Audit</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Compliance Audit 2024</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Auditee</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Acme Corporation</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Status</p>
                            <p>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                    Menunggu Verifikasi Final
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr class="dark:border-gray-700">

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Root Cause Analysis (RCA) dari Auditee</p>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Auditee telah mengidentifikasi bahwa kurangnya pelatihan pada staf baru menjadi penyebab utama kesalahan entri data.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Action Plan dari Auditee</p>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Akan dilaksanakan sesi pelatihan wajib untuk semua staf terkait pada tanggal 15 bulan depan. Modul pelatihan baru sedang dikembangkan.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Komunikasi Terakhir</p>
                            <!-- <blockquote class="mt-2 pl-4 border-l-4 border-gray-200 dark:border-gray-600"> -->
                                <p class="italic text-gray-600 dark:text-gray-400">"Kami telah melampirkan bukti pendaftaran untuk sesi pelatihan. Kami yakin ini akan mengatasi masalah yang ada."</p>
                                <cite class="block text-right text-gray-500 mt-2">- Jane Doe (Auditee)</cite>
                            </blockquote>
                        </div>
                    </div>
                </div>

                 <!-- <div class="pt-6 flex justify-end space-x-3">
                    <button type="button" class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-300">
                        Reject
                    </button>
                    <button type="submit" class="bg-green-500 text-white font-bold py-2 px-5 rounded-lg shadow-md hover:bg-green-600 transition duration-300">
                        Approve & Close
                    </button>
                </div> -->
                
            </div>
        </div>
    </div>
</x-app-layout>