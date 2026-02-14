<div class="d-flex justify-content-between align-items-center">
    {{-- Info de registros (esquerda) --}}
    <div class="text-muted fs-7">
        Exibindo {{ $paginator->firstItem() ?? 0 }} a {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} registros
    </div>

    {{-- Paginação (direita) --}}
    @if ($paginator->hasPages())
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item previous disabled">
                    <a href="#" class="page-link"><i class="previous"></i></a>
                </li>
            @else
                <li class="page-item previous">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link"><i class="previous"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $showPages = [];

                // Se tem 7 ou menos páginas, mostra todas
                if ($lastPage <= 7) {
                    $showPages = range(1, $lastPage);
                } else {
                    // Sempre mostra primeira e última
                    $showPages[] = 1;
                    $showPages[] = 2;

                    // Páginas ao redor da atual
                    if ($currentPage > 4 && $currentPage < $lastPage - 3) {
                        $showPages[] = '...';
                        $showPages[] = $currentPage - 1;
                        $showPages[] = $currentPage;
                        $showPages[] = $currentPage + 1;
                        $showPages[] = '...';
                    } elseif ($currentPage <= 4) {
                        // Próximo do início
                        for ($i = 3; $i <= min(5, $lastPage - 2); $i++) {
                            $showPages[] = $i;
                        }
                        $showPages[] = '...';
                    } else {
                        // Próximo do fim
                        $showPages[] = '...';
                        for ($i = max(3, $lastPage - 4); $i <= $lastPage - 2; $i++) {
                            $showPages[] = $i;
                        }
                    }

                    $showPages[] = $lastPage - 1;
                    $showPages[] = $lastPage;

                    // Remove duplicatas mantendo ordem
                    $showPages = array_unique($showPages);
                }
            @endphp

            @foreach ($showPages as $page)
                @if ($page === '...')
                    <li class="page-item disabled"><a href="#" class="page-link">...</a></li>
                @elseif ($page == $currentPage)
                    <li class="page-item active"><a href="#" class="page-link">{{ $page }}</a></li>
                @else
                    <li class="page-item"><a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link"><i class="next"></i></a>
                </li>
            @else
                <li class="page-item next disabled">
                    <a href="#" class="page-link"><i class="next"></i></a>
                </li>
            @endif
        </ul>
    @endif
</div>
