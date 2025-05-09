@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <style>
            /* Pagination responsiveness for all pages */
            .pagination {
                margin: 1rem 0;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }
            
            .page-link {
                position: relative;
                min-width: 2.5rem;
                text-align: center;
                transition: all 0.2s;
                padding: 0.5rem 0.75rem;
                color: var(--primary-color, #198754);
                background-color: #fff;
                border: 1px solid #dee2e6;
            }
            
            .page-item.active .page-link {
                background-color: var(--primary-color, #198754);
                border-color: var(--primary-color, #198754);
                color: white;
            }

            .page-link:hover {
                color: var(--primary-color, #198754);
                background-color: #e9ecef;
                border-color: #dee2e6;
            }

            .page-item.disabled .page-link {
                color: #6c757d;
                pointer-events: none;
                background-color: #fff;
                border-color: #dee2e6;
            }
            
            /* Mobile optimizations */
            @media (max-width: 576px) {
                .pagination {
                    gap: 0.125rem;
                }
                
                .page-link {
                    padding: 0.375rem 0.5rem;
                    font-size: 0.875rem;
                    min-width: 2rem;
                }
                
                .page-item {
                    margin: 0;
                }
                
                .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.prev):not(.next) {
                    display: none;
                }
                
                .pagination .page-item.active .page-link {
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                }

                .pagination-info {
                    font-size: 0.875rem;
                    text-align: center;
                }
            }
            
            /* Tablet optimizations */
            @media (min-width: 577px) and (max-width: 991.98px) {
                .pagination {
                    gap: 0.25rem;
                }
                
                .page-link {
                    padding: 0.5rem 0.75rem;
                    min-width: 2.25rem;
                }
            }
        </style>
        <div class="d-flex flex-column align-items-center">
            <!-- Results Info -->
            <div class="mb-2">
                <p class="text-muted small mb-0 pagination-info">
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
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
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
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
