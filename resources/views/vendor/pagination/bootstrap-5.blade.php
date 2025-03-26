@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <style>
            .pagination .page-item.active .page-link {
                background-color: #198754;
                border-color: #198754;
                color: white;
            }
            .pagination .page-link {
                color: #198754;
            }
            .pagination .page-link:hover {
                color: #157347;
            }
            .pagination .page-item.disabled .page-link {
                color: #6c757d;
            }
        </style>
        <div class="d-flex flex-column align-items-center">
            <!-- Results Info -->
            <div class="mb-3">
                <p class="text-muted small mb-0">
                    Showing
                    @if ($paginator->firstItem())
                        <span class="fw-medium">{{ $paginator->firstItem() }}</span>
                        to
                        <span class="fw-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    of
                    <span class="fw-medium">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <!-- Pagination -->
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&lt;&lt; Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lt;&lt; Previous</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &gt;&gt;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next &gt;&gt;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
