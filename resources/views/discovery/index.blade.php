<x-public-layout :title="$seo['title']" :description="$seo['description']" :canonical="$seo['canonical']" :noindex="$seo['noindex']" :social-image="$seo['socialImage']" :robots-follow="true">
    <div class="mx-auto max-w-6xl px-5 pt-8 sm:px-8 sm:pt-12">
        <x-breadcrumbs :items="$breadcrumbs" :current-url="$seo['canonical']" />
    </div>
    <x-discovery-identity :identity="$identity" :city="$city" />

    <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">
    <form method="GET" action="{{ $baseUrl }}" data-discovery-filters class="mt-8 min-w-0 rounded-3xl border border-brand-border bg-white p-5 sm:p-6" aria-labelledby="titulo-filtros">
        <h2 id="titulo-filtros" class="font-display text-lg font-extrabold sm:text-xl">Filtrar negocios</h2>
        <div class="mt-4 grid min-w-0 gap-4 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end">
            @unless($category)
                <div class="min-w-0 lg:flex-1">
                    <label for="categoria" class="mb-2 block text-sm font-semibold">Categoría</label>
                    <select id="categoria" name="categoria" class="min-h-12 w-full min-w-0 max-w-full rounded-xl border border-brand-muted bg-white px-3 py-3 text-base focus-visible:outline-offset-2">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $option)
                            <option value="{{ $option->slug }}" @selected($filterCategory?->id === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endunless
            @unless($area)
                <div class="min-w-0 lg:flex-1">
                    <label for="zona" class="mb-2 block text-sm font-semibold">Zona</label>
                    <select id="zona" name="zona" class="min-h-12 w-full min-w-0 max-w-full rounded-xl border border-brand-muted bg-white px-3 py-3 text-base focus-visible:outline-offset-2">
                        <option value="">Todas las zonas</option>
                        @foreach($areas as $option)
                            <option value="{{ $option->slug }}" @selected($filterArea?->id === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endunless
            <button type="submit" data-filter-submit class="min-h-12 min-w-0 max-w-full rounded-xl border border-brand-text bg-brand-action px-6 py-3 font-bold text-brand-text hover:underline active:brightness-95">Aplicar</button>
            @if(count($filters))
                <a href="{{ $baseUrl }}" class="inline-flex min-h-12 min-w-0 max-w-full items-center justify-center rounded-xl px-3 py-3 text-center font-semibold underline underline-offset-4">Limpiar filtros</a>
            @endif
        </div>
        <p role="status" data-filter-status class="mt-3 text-sm text-brand-muted"></p>
    </form>

    <section aria-labelledby="titulo-resultados" class="mt-8 sm:mt-10">
        <div class="mb-5 flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
            <h2 id="titulo-resultados" class="font-display text-xl font-extrabold sm:text-2xl">{{ $listings->total() }} {{ $listings->total() === 1 ? 'negocio' : 'negocios' }}</h2>
            @if($listings->isNotEmpty())<p class="text-sm text-brand-muted">Más recientes primero</p>@endif
        </div>
        @if($listings->isNotEmpty())
            <div class="grid gap-3">
                @foreach($listings as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
            {{ $listings->onEachSide(1)->links('components.discovery-pagination') }}
        @else
            <div class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8">
                <h3 class="font-display text-2xl font-extrabold">{{ count($filters) ? 'No hay negocios con estos filtros' : 'Todavía no hay negocios publicados aquí' }}</h3>
                <p class="mt-3 max-w-2xl leading-relaxed text-brand-muted">{{ count($filters) ? 'Prueba otra categoría o zona para ver más opciones.' : 'Las fichas aparecerán cuando el equipo termine de revisarlas. Puedes consultar los negocios de otras categorías o zonas.' }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    @if(count($filters))<a href="{{ $baseUrl }}" class="inline-flex min-h-12 max-w-full items-center justify-center rounded-xl border border-brand-text bg-brand-action px-5 py-3 text-center font-bold text-brand-text hover:underline">Limpiar filtros</a>@endif
                    <a href="{{ \App\Support\PublicUrl::businesses($city) }}" class="inline-flex min-h-12 items-center rounded-xl px-2 py-3 font-bold underline underline-offset-4">Ver todos los negocios</a>
                </div>
            </div>
        @endif
    </section>
    </div>
</x-public-layout>
