<x-public-layout :title="$title" :description="$description" :canonical="$canonical" :noindex="$preview" :social-image="($cover ?? $logo)?->getUrl()">
    <x-slot:head>
        @unless($preview)<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endunless

    </x-slot:head>
    @if($preview)<div role="status" class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-amber-950"><strong>Previsualización privada.</strong> Estado: {{ \App\Filament\Resources\Listings\ListingResource::statuses()[$listing->status] }}. Esta vista no publica la ficha.</div>
@endif

    @if($listing->is_demo)<p role="note" class="mb-5 rounded-2xl border border-brand-border bg-white p-4 font-semibold">DEMO · Negocio sintético para revisión local. No representa un negocio real.</p>@endif
    @php($publicCityNavigation = $listing->city->is_public && $listing->city->slug === 'playa-del-carmen')
    <x-breadcrumbs :items="[
        ['label' => 'Inicio', 'url' => route('bootstrap')],
        ['label' => $listing->city->name, 'url' => $publicCityNavigation ? route('cities.show', ['city' => $listing->city->slug]) : null],
        ['label' => $listing->name, 'url' => null],
    ]" />
    <section aria-labelledby="titulo-ficha" class="overflow-hidden rounded-[32px] bg-brand-deep text-white shadow-lg">
        @if($cover)<img src="{{ $cover->getUrl($cover->hasGeneratedConversion('card') ? 'card' : '') }}" alt="{{ $listing->image_alt ?: $listing->name }}" class="max-h-96 w-full object-cover" fetchpriority="high">
@endif

        <div class="p-6 sm:p-9">
            <div class="mb-4 flex flex-wrap gap-2 text-sm font-semibold">
                @if($publicCityNavigation && $listing->primaryCategory->is_active && $listing->primaryCategory->listing_type === 'business')
                    <a href="{{ route('categories.show', ['city' => $listing->city->slug, 'category' => $listing->primaryCategory->slug]) }}" class="inline-flex min-h-11 min-w-0 max-w-full items-center rounded-lg px-3 py-2 wrap-anywhere underline underline-offset-4" style="background-color: {{ $listing->primaryCategory->identityBackground() }}; color: {{ $listing->primaryCategory->identityForeground() }}">{{ $listing->primaryCategory->name }}</a>
                @else
                    <span class="inline-flex min-h-11 min-w-0 max-w-full items-center rounded-lg px-3 py-2 wrap-anywhere" style="background-color: {{ $listing->primaryCategory->identityBackground() }}; color: {{ $listing->primaryCategory->identityForeground() }}">{{ $listing->primaryCategory->name }}</span>
                @endif
                @if($listing->area?->is_active && (! $listing->area->is_demo || app()->environment(['local', 'testing'])))
                    @if($publicCityNavigation)
                        <a href="{{ route('areas.show', ['city' => $listing->city->slug, 'area' => $listing->area->slug]) }}" class="inline-flex min-h-11 min-w-0 max-w-full items-center rounded-lg px-3 py-2 wrap-anywhere underline underline-offset-4" style="background-color: {{ $listing->area->identityBackground() }}; color: {{ $listing->area->identityForeground() }}">{{ $listing->area->name }}</a>
                    @else
                        <span class="inline-flex min-h-11 min-w-0 max-w-full items-center rounded-lg px-3 py-2 wrap-anywhere" style="background-color: {{ $listing->area->identityBackground() }}; color: {{ $listing->area->identityForeground() }}">{{ $listing->area->name }}</span>
                    @endif
                @endif
            </div>
            <div class="flex items-center gap-4 sm:gap-5">
                @if($logo)<img src="{{ $logo->getUrl() }}" alt="{{ $listing->logo_alt ?: $listing->name }}" width="96" height="96" class="h-16 w-16 shrink-0 rounded-2xl bg-white object-contain p-2 sm:h-24 sm:w-24">
                @endif
                <h1 id="titulo-ficha" class="min-w-0 font-display text-3xl leading-tight font-extrabold wrap-anywhere sm:text-5xl">{{ $listing->name }}</h1>
            </div>
            @if($listing->summary)<p class="mt-4 max-w-3xl text-lg leading-relaxed text-white/90">{{ $listing->summary }}</p>
@endif

            @if($listing->operating_status !== 'open')<p class="mt-4 inline-block rounded-xl bg-white/15 px-4 py-2 font-semibold">{{ $listing->operating_status === 'temporarily_closed' ? 'Cerrado temporalmente' : 'Cerrado permanentemente' }}</p>
@endif

            @if($listing->google_rating !== null || $listing->google_review_count !== null)<p class="mt-4 text-sm text-white/85">Google: @if($listing->google_rating !== null){{ $listing->google_rating }} de 5
@endif
 @if($listing->google_review_count !== null) · {{ number_format($listing->google_review_count) }} reseñas
@endif
 @if($listing->google_reviewed_at) · Revisado {{ $listing->google_reviewed_at->format('d/m/Y') }}
@endif
</p>
@endif

            @if(count($contacts))<div class="mt-6 flex flex-wrap gap-3 border-t border-white/20 pt-6" aria-label="Contactar o visitar">
                @foreach($contacts as $contact)<a href="{{ $contact['url'] }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/40 px-5 py-3 font-semibold hover:bg-white/10">{{ $contact['label'] }}</a>
@endforeach

            </div>
@endif

        </div>
    </section>
    <div class="mt-7 grid items-start gap-7 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
        <div class="grid min-w-0 gap-7">
            @if($listing->description)<section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="font-display text-2xl font-extrabold">Sobre este negocio</h2><p class="mt-4 leading-relaxed whitespace-pre-line wrap-anywhere">{{ $listing->description }}</p></section>
@endif

            @if($listing->reasons)<section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="font-display text-2xl font-extrabold">Por qué visitarlo</h2><p class="mt-4 leading-relaxed whitespace-pre-line">{{ $listing->reasons }}</p></section>
@endif

            @if($gallery->isNotEmpty())<section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="mb-5 font-display text-2xl font-extrabold">En imágenes</h2><div class="grid gap-4 sm:grid-cols-2">@foreach($gallery as $photo)<a href="{{ $photo->getUrl() }}" class="block overflow-hidden rounded-2xl"><img src="{{ $photo->getUrl($photo->hasGeneratedConversion('card') ? 'card':'') }}" alt="{{ $photo->getCustomProperty('alt') ?: ($listing->image_alt ?: $listing->name) }}" class="aspect-[4/3] w-full object-cover" loading="lazy" width="600" height="450"></a>
@endforeach
</div></section>
@endif

            @if(filled($listing->faqs))<section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="mb-5 font-display text-2xl font-extrabold">Preguntas frecuentes</h2><div class="divide-y divide-brand-border">@foreach($listing->faqs as $faq)<details class="py-4"><summary class="cursor-pointer rounded-lg py-2 font-semibold">{{ $faq['question'] }}</summary><p class="pt-3 leading-relaxed whitespace-pre-line">{{ $faq['answer'] }}</p></details>
@endforeach
</div></section>
@endif

        </div>
        <aside class="grid min-w-0 gap-7" aria-label="Información práctica">
            <section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="font-display text-2xl font-extrabold">Información práctica</h2><dl class="mt-5 grid gap-5">
                @if($listing->address)<div><dt class="text-sm font-semibold text-brand-muted">Dirección o referencia</dt><dd class="mt-1 whitespace-pre-line">{{ $listing->address }}</dd></div>
@endif

                @if($listing->opening_hours)<div><dt class="text-sm font-semibold text-brand-muted">Horario</dt><dd class="mt-1 whitespace-pre-line">{{ $listing->opening_hours }}</dd></div>
@endif

                <div><dt class="text-sm font-semibold text-brand-muted">Ciudad</dt><dd class="mt-1"><span class="inline-block rounded-lg px-3 py-1" style="background-color: {{ $listing->city->identityBackground() }}; color: {{ $listing->city->identityForeground() }}">{{ $listing->city->name }}</span></dd></div>
                @if($listing->phone)<div><dt class="text-sm font-semibold text-brand-muted">Teléfono</dt><dd class="mt-1">{{ $listing->phone }}</dd></div>
@endif

            </dl>
            @if($document)<a href="{{ $preview ? $document->getUrl() : route('listings.contact',['listing'=>$listing->slug,'kind'=>'document']) }}" class="mt-6 flex min-h-12 items-center justify-center rounded-xl bg-brand-action px-5 py-3 text-center font-bold text-brand-text">{{ $listing->document_button_label }}</a><p class="mt-2 text-center text-sm text-brand-muted">PDF · {{ $document->human_readable_size }}</p>
@endif

            </section>
            <section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="font-display text-2xl font-extrabold">Comparte este negocio</h2><p class="mt-3 leading-relaxed text-brand-muted">Envía su ficha a quien le pueda servir.</p><button type="button" data-share-url="{{ $canonical }}" data-share-title="{{ $listing->name }}" class="mt-5 min-h-12 w-full rounded-xl bg-brand-action px-5 py-3 font-bold text-brand-text">Compartir ficha</button><p data-share-status role="status" class="mt-3 text-sm text-brand-muted"></p><label for="ficha-url" class="mt-3 block text-sm text-brand-muted">Enlace de la ficha</label><input id="ficha-url" readonly value="{{ $canonical }}" class="mt-2 min-h-12 w-full min-w-0 rounded-xl border border-brand-border px-3 text-sm" onclick="this.select()"></section>
            @if($reportsAvailable)<section class="rounded-3xl border border-brand-border bg-white p-6 sm:p-8"><h2 class="font-display text-xl font-extrabold">¿Hay un dato incorrecto?</h2><p class="mt-3 text-brand-muted">Ayúdanos a mantener esta información al día.</p><a class="mt-4 inline-flex min-h-12 items-center rounded-lg font-bold underline underline-offset-4" href="{{ route('listings.report',['listing'=>$listing->slug]) }}">Reportar datos incorrectos</a></section>
@endif

        </aside>
    </div>
</x-public-layout>
