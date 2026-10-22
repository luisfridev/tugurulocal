@if($paginator->hasPages())
    <nav aria-label="Páginas de negocios" class="mt-8 flex flex-col items-center gap-4 sm:mt-10">
        <p class="text-sm text-brand-muted">Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} negocios</p>
        <div class="flex max-w-full flex-wrap justify-center gap-2">
            @unless($paginator->onFirstPage())
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-brand-muted bg-white px-4 py-2 font-semibold hover:bg-brand-action">Anterior</a>
            @endunless
            @foreach($elements as $element)
                @if(is_string($element))
                    <span class="inline-flex min-h-11 min-w-11 items-center justify-center" aria-hidden="true">{{ $element }}</span>
                @endif
                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page === $paginator->currentPage())
                            <span aria-current="page" aria-label="Página {{ $page }}" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-brand-deep px-3 py-2 font-bold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Página {{ $page }}" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-brand-muted bg-white px-3 py-2 font-semibold hover:bg-brand-action">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-brand-muted bg-white px-4 py-2 font-semibold hover:bg-brand-action">Siguiente</a>
            @endif
        </div>
    </nav>
@endif
