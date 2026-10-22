@props(['listing'])

@php($logo = $listing->getFirstMedia('logo'))
<article class="min-w-0 rounded-3xl border border-brand-border bg-white">
    <a href="{{ route('listings.show', ['listing' => $listing->slug]) }}" class="group flex h-full min-w-0 flex-col gap-4 rounded-3xl p-5 hover:bg-brand-page/50 active:bg-brand-page sm:p-6">
        <div class="flex min-w-0 flex-wrap items-start gap-4">
            @if($logo)
                <img src="{{ $logo->getUrl($logo->hasGeneratedConversion('card') ? 'card' : '') }}" alt="{{ $listing->logo_alt ?: $listing->name }}" width="72" height="72" class="h-16 w-16 shrink-0 rounded-2xl border border-brand-border bg-white object-contain p-1 sm:h-18 sm:w-18" loading="lazy">
            @endif
            <div class="min-w-0 flex-1 basis-28">
                <h3 class="font-display text-xl leading-snug font-extrabold wrap-anywhere group-hover:underline decoration-2 underline-offset-4">{{ $listing->name }}</h3>
                @if($listing->is_demo)
                    <p class="mt-1 text-sm font-semibold text-brand-muted">DEMO · Negocio sintético</p>
                @endif
            </div>
        </div>
        @if($listing->summary)
            <p class="text-base leading-relaxed wrap-anywhere">{{ $listing->summary }}</p>
        @endif
        @if($listing->operating_status !== 'open')
            <p class="text-sm font-bold">{{ $listing->operating_status === 'temporarily_closed' ? 'Cerrado temporalmente' : 'Cerrado permanentemente' }}</p>
        @endif
        <div class="mt-auto flex min-w-0 flex-wrap gap-x-3 gap-y-2 text-sm text-brand-muted">
            @if($listing->primaryCategory?->is_active)
                <span class="min-w-0 max-w-full wrap-anywhere">{{ $listing->primaryCategory->name }}</span>
            @endif
            @if($listing->area?->is_active && (! $listing->area->is_demo || app()->environment(['local', 'testing'])))
                <span class="min-w-0 max-w-full wrap-anywhere">{{ $listing->area->name }}</span>
            @endif
        </div>
    </a>
</article>
