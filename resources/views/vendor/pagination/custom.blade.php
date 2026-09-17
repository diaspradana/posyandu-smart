@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="custom-pagination-container">
        {{-- Info Ringkasan Data --}}
        <div class="pagination-info">
            Menampilkan
            <span class="pagination-highlight">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="pagination-highlight">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="pagination-highlight">{{ $paginator->total() }}</span>
            data
        </div>

        {{-- Navigasi Tombol Halaman --}}
        <div class="pagination-nav">
            <ul class="pagination-list">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                        <span class="page-link nav-btn" aria-hidden="true">&laquo; Sebelumnya</span>
                    </li>
                @else
                    <li class="page-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link nav-btn" aria-label="Sebelumnya">&laquo; Sebelumnya</a>
                    </li>
                @endif

                {{-- Nomor-nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link dots">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Selanjutnya --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link nav-btn" aria-label="Selanjutnya">Selanjutnya &raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Selanjutnya">
                        <span class="page-link nav-btn" aria-hidden="true">Selanjutnya &raquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@elseif ($paginator->total() > 0)
    <div class="custom-pagination-container single-page">
        <div class="pagination-info">
            Menampilkan <span class="pagination-highlight">1</span> sampai <span class="pagination-highlight">{{ $paginator->total() }}</span> dari <span class="pagination-highlight">{{ $paginator->total() }}</span> data
        </div>
    </div>
@endif
