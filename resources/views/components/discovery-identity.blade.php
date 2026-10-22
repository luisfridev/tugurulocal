@props(['identity', 'home' => false, 'city'])

<section aria-labelledby="titulo-pagina" class="rounded-[32px] p-6 sm:p-9 lg:p-12" style="background-color: {{ $identity['background'] }}; color: {{ $identity['foreground'] }}">
    <div class="flex flex-col items-start gap-4 sm:flex-row sm:gap-6">
        @if($identity['iconUrl'])
            <img src="{{ $identity['iconUrl'] }}" alt="{{ $identity['iconAlt'] }}" width="80" height="80" class="h-14 w-14 shrink-0 rounded-2xl object-contain sm:h-20 sm:w-20">
        @endif
        <div class="min-w-0 max-w-full">
            <h1 id="titulo-pagina" class="max-w-3xl font-display text-4xl leading-tight font-extrabold wrap-anywhere sm:text-5xl">{{ $identity['title'] }}</h1>
            @if($identity['tagline'])
                <p class="mt-4 max-w-2xl text-lg leading-relaxed font-semibold wrap-anywhere sm:text-xl">{{ $identity['tagline'] }}</p>
            @endif
            @if($identity['description'])
                <div class="taxonomy-content mt-4 max-w-2xl leading-relaxed wrap-anywhere">{!! $identity['description'] !!}</div>
            @endif
            @if($home)
                <a href="{{ route('businesses.index', ['city' => $city->slug]) }}" class="mt-6 inline-flex min-h-12 max-w-full items-center justify-center rounded-xl border border-brand-text bg-brand-action px-6 py-3 text-center font-bold text-brand-text hover:underline active:brightness-95">Ver negocios</a>
            @endif
        </div>
    </div>
</section>
