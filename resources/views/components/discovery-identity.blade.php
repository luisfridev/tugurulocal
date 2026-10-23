@props(['identity', 'home' => false, 'city'])

<section aria-labelledby="titulo-pagina" class="bg-brand-deep text-white" style="background-color: {{ $identity['background'] }}; color: {{ $identity['foreground'] }}">
    <div class="mx-auto flex max-w-6xl items-start gap-4 px-5 py-16 sm:gap-6 sm:px-8 sm:py-24">
        @if($identity['iconUrl'])
            <img src="{{ $identity['iconUrl'] }}" alt="{{ $identity['iconAlt'] }}" width="80" height="80" class="mt-1 h-14 w-14 shrink-0 rounded-2xl object-contain sm:h-20 sm:w-20">
        @endif
        <div class="min-w-0 max-w-full">
            <h1 id="titulo-pagina" class="max-w-3xl font-display leading-[1.05] font-extrabold wrap-anywhere {{ $home ? 'text-4xl sm:text-5xl lg:text-[60px]' : 'text-3xl sm:text-4xl' }}">{{ $identity['title'] }}</h1>
            @if($identity['tagline'])
                <p class="mt-4 max-w-2xl text-base leading-6 font-semibold wrap-anywhere sm:text-lg sm:leading-7">{{ $identity['tagline'] }}</p>
            @endif
            @if($identity['description'])
                <div class="taxonomy-content mt-4 max-w-2xl leading-6 wrap-anywhere">{!! $identity['description'] !!}</div>
            @endif
            @if($home)
                <a href="{{ \App\Support\PublicUrl::businesses($city) }}" class="mt-6 inline-flex min-h-12 max-w-full items-center justify-center rounded-xl border border-brand-text bg-brand-action px-6 py-3 text-center font-bold text-brand-text hover:underline active:brightness-95">Ver negocios</a>
            @endif
        </div>
    </div>
</section>
