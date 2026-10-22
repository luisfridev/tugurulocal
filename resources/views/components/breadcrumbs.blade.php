@props(['items'])

<nav aria-label="Ubicación" class="mb-5 text-sm text-brand-muted">
    <ol class="flex flex-wrap items-center gap-x-2">
        @foreach($items as $item)
            <li class="flex min-w-0 max-w-full items-center gap-2">
                @unless($loop->first)<span aria-hidden="true">/</span>@endunless
                @if($item['url'])
                    <a href="{{ $item['url'] }}" class="inline-flex min-h-11 min-w-0 items-center rounded-md py-2 wrap-anywhere underline decoration-brand-muted/50 underline-offset-4 hover:text-brand-text">{{ $item['label'] }}</a>
                @else
                    <span @if($loop->last) aria-current="page" @endif class="py-2 wrap-anywhere">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
