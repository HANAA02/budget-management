@props(['pagination'])

@if ($pagination->hasPages())
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center">
            {{-- Précédent --}}
            @if ($pagination->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $pagination->previousPageUrl() }}" rel="prev">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Numéros de page --}}
            @foreach ($pagination->getUrlRange(max(1, $pagination->currentPage() - 2), min($pagination->lastPage(), $pagination->currentPage() + 2)) as $page => $url)
                @if ($page == $pagination->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($pagination->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $pagination->nextPageUrl() }}" rel="next">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
