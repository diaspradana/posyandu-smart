@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="custom-pagination-container">
        <div class="pagination-info">
            Halaman <strong>{{ $paginator->currentPage() }}</strong>
        </div>

        <div class="pagination-nav">
            <ul class="pagination-list">
                {{-- Previous Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link nav-btn">&laquo; Sebelumnya</span>
                    </li>
                @else
                    <li class="page-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link nav-btn">&laquo; Sebelumnya</a>
                    </li>
                @endif

                {{-- Next Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link nav-btn">Selanjutnya &raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link nav-btn">Selanjutnya &raquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
