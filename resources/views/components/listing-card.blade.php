@props(['listing'])

@php($logo = $listing->getFirstMedia('logo'))
<article class="min-w-0 rounded-3xl border border-brand-border bg-white">
    <a href="{{ \App\Support\PublicUrl::listing($listing) }}" class="listing-row group flex min-w-0 items-start gap-4 rounded-3xl p-3.5 hover:bg-white active:bg-brand-page sm:items-center sm:gap-4 sm:p-4">
        @if($logo)
            <img src="{{ $logo->getUrl($logo->hasGeneratedConversion('card') ? 'card' : '') }}" alt="{{ $listing->logo_alt ?: $listing->name }}" width="72" height="72" class="h-16 w-16 shrink-0 rounded-2xl border border-brand-border bg-white object-contain p-1" loading="lazy">
        @endif
        <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
            <div class="min-w-0 flex-1">
                <h3 class="font-display text-base leading-6 font-extrabold wrap-anywhere group-hover:underline decoration-2 underline-offset-4">{{ $listing->name }}</h3>
                @if($listing->is_demo)
                    <p class="mt-1 text-xs font-semibold text-brand-muted">DEMO · Negocio sintético</p>
                @endif
                @if($listing->summary)
                    <p class="mt-1.5 leading-6 wrap-anywhere">{{ $listing->summary }}</p>
                @endif
                @if($listing->operating_status !== 'open')
                    <p class="mt-2 text-sm font-bold">{{ $listing->operating_status === 'temporarily_closed' ? 'Cerrado temporalmente' : 'Cerrado permanentemente' }}</p>
                @endif
            </div>
            <div class="flex min-w-0 flex-wrap gap-x-3 gap-y-1.5 text-xs font-semibold text-brand-muted sm:max-w-[32%] sm:justify-end sm:text-right">
                @if($listing->primaryCategory?->is_active)
                    <span class="min-w-0 max-w-full wrap-anywhere">{{ $listing->primaryCategory->name }}</span>
                @endif
                @if($listing->area?->is_active && (! $listing->area->is_demo || app()->environment(['local', 'testing'])))
                    <span class="min-w-0 max-w-full wrap-anywhere">{{ $listing->area->name }}</span>
                @endif
            </div>
        </div>
    </a>
</article>
