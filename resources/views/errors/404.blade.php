<x-public-layout title="Página no encontrada | Tu Gurú Local" :noindex="true" :robots-follow="true">
    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-16">
        <section aria-labelledby="titulo-error" class="grid gap-5 rounded-[22px] border border-brand-detail-border bg-brand-detail p-6 text-white sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center sm:gap-8 sm:p-10">
            <p aria-hidden="true" class="whitespace-nowrap font-display text-5xl leading-none font-extrabold text-brand-detail-accent sm:text-7xl">404</p>
            <div>
                <p class="mb-3 font-semibold text-brand-detail-accent">Error 404</p>
                <h1 id="titulo-error" class="max-w-2xl font-display text-3xl leading-9 font-extrabold sm:text-4xl sm:leading-tight">No encontramos esta página</h1>
                <p class="mt-4 max-w-xl text-base leading-6 text-white/90">El contenido puede haber cambiado o ya no estar disponible. También puede que los filtros no sean válidos.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('businesses.index', ['city' => 'playa-del-carmen']) }}" class="inline-flex min-h-12 max-w-full items-center justify-center rounded-xl border border-brand-text bg-brand-action px-5 py-3 text-center font-bold text-brand-text hover:underline">Ver negocios</a>
                    <a href="{{ route('bootstrap') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl px-5 py-3 font-semibold underline underline-offset-4 hover:bg-white/10">Volver al inicio</a>
                </div>
            </div>
        </section>
    </div>
</x-public-layout>
