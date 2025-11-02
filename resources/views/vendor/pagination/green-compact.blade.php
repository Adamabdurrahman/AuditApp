@if ($paginator->hasPages())
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6 text-base text-gray-900 dark:text-gray-200 font-medium">
            {{-- 📊 Info jumlah data --}}
            <div>
                Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} temuan
            </div>

            {{-- 📍 Navigasi halaman --}}
            <nav role="navigation" aria-label="Pagination" class="flex items-center flex-wrap justify-center sm:justify-start gap-1 mt-3 sm:mt-0">
                {{-- First --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3.5 py-2 text-gray-400 cursor-not-allowed rounded-md font-medium">First</span>
                @else
                    <a href="{{ $paginator->url(1) }}" class="px-3.5 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 font-medium transition">First</a>
                @endif

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3.5 py-2 text-gray-400 cursor-not-allowed rounded-md font-medium"><</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3.5 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 font-medium transition"><</a>
                @endif

                {{-- Window halaman --}}
                @php
                    $window = 3;
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $start = max(1, floor(($current - 1) / $window) * $window + 1);
                    $end = min($start + $window - 1, $last);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <span class="px-3.5 py-2 bg-green-600 text-white rounded-md font-bold">{{ $i }}</span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="px-3.5 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 font-medium transition">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3.5 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 font-medium transition">></a>
                @else
                    <span class="px-3.5 py-2 text-gray-400 cursor-not-allowed rounded-md font-medium">></span>
                @endif

                {{-- Last --}}
                @if ($paginator->currentPage() == $paginator->lastPage())
                    <span class="px-3.5 py-2 text-gray-400 cursor-not-allowed rounded-md font-medium">Last</span>
                @else
                    <a href="{{ $paginator->url($paginator->lastPage()) }}" class="px-3.5 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 font-medium transition">Last</a>
                @endif
            </nav>
        </div>
    </div>
@endif