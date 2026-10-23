<x-public-layout :title="$seo['title']" :description="$seo['description']" :canonical="$seo['canonical']" :noindex="$seo['noindex']" :social-image="$seo['socialImage']" :robots-follow="true">
    <x-discovery-identity :identity="$identity" :city="$city" :home="true" />

    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-14">
    @if($categories->where('public_count', '>', 0)->isNotEmpty())
        <section aria-labelledby="titulo-categorias">
            <h2 id="titulo-categorias" class="font-display text-2xl leading-8 font-extrabold sm:text-3xl sm:leading-9">Negocios por categoría</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($categories->where('public_count', '>', 0) as $category)
                    <a href="{{ route('categories.show', ['city' => $city->slug, 'category' => $category->slug]) }}" class="flex min-h-20 items-center rounded-3xl border border-brand-border border-l-4 bg-white px-5 py-5 font-display text-lg font-extrabold text-brand-text wrap-anywhere hover:underline underline-offset-4 active:brightness-95" style="border-left-color: {{ $category->identityBackground() }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </section>
    @endif

    @if($featured->isNotEmpty())
        <section aria-labelledby="titulo-seleccion" class="mt-10 sm:mt-14">
            <div class="mb-5 max-w-2xl">
                <h2 id="titulo-seleccion" class="font-display text-2xl leading-8 font-extrabold sm:text-3xl sm:leading-9">Selección de Tu Gurú Local</h2>
                <p class="mt-3 leading-relaxed text-brand-muted">Negocios elegidos por nuestro equipo editorial.</p>
            </div>
            <div class="grid gap-3">
                @foreach($featured as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
        </section>
    @endif

    @if($areas->where('public_count', '>', 0)->isNotEmpty())
        <section aria-labelledby="titulo-zonas" class="mt-10 grid gap-4 border-y border-brand-border py-8 sm:mt-14 md:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] md:gap-8">
            <div>
                <h2 id="titulo-zonas" class="font-display text-2xl leading-8 font-extrabold">Elige una zona</h2>
                <p class="mt-2 leading-relaxed text-brand-muted">Consulta los negocios de cada zona.</p>
            </div>
            <ul class="grid gap-2 sm:grid-cols-2">
                @foreach($areas->where('public_count', '>', 0) as $area)
                    <li><a href="{{ route('areas.show', ['city' => $city->slug, 'area' => $area->slug]) }}" class="flex min-h-12 items-center rounded-xl px-4 py-3 font-semibold wrap-anywhere underline decoration-brand-muted/50 underline-offset-4 hover:bg-white active:bg-white">{{ $area->name }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($recent->isNotEmpty())
        <section aria-labelledby="titulo-recientes" class="mt-10 sm:mt-14">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
                <div>
                    <h2 id="titulo-recientes" class="font-display text-2xl leading-8 font-extrabold sm:text-3xl sm:leading-9">Recién publicados</h2>
                    <p class="mt-3 leading-relaxed text-brand-muted">Las últimas fichas que añadimos al directorio.</p>
                </div>
                <a href="{{ route('businesses.index', ['city' => $city->slug]) }}" class="inline-flex min-h-11 items-center rounded-lg py-2 font-bold underline underline-offset-4">Ver todos los negocios</a>
            </div>
            <div class="grid gap-3">
                @foreach($recent as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
        </section>
    @endif

    @if($featured->isEmpty() && $recent->isEmpty())
        <section aria-labelledby="titulo-sin-negocios" class="mt-8 rounded-3xl border border-brand-border bg-white p-6 sm:p-8">
            <h2 id="titulo-sin-negocios" class="font-display text-2xl font-extrabold">Consulta el directorio de negocios</h2>
            <p class="mt-3 max-w-2xl leading-relaxed text-brand-muted">En el directorio puedes consultar las fichas publicadas y filtrar por categoría o zona.</p>
            <a href="{{ route('businesses.index', ['city' => $city->slug]) }}" class="mt-5 inline-flex min-h-12 items-center rounded-xl px-2 py-3 font-bold underline underline-offset-4">Ver negocios</a>
        </section>
    @endif
    </div>
</x-public-layout>
