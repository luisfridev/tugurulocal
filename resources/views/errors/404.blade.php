<x-public-layout title="Página no encontrada | Tu Gurú Local" :noindex="true" :robots-follow="true">
    <section aria-labelledby="titulo-error" class="rounded-[32px] bg-brand-deep p-6 text-white sm:p-10">
        <p class="mb-4 font-semibold text-brand-action">Error 404</p>
        <h1 id="titulo-error" class="max-w-2xl font-display text-4xl leading-tight font-extrabold sm:text-5xl">No encontramos esta página</h1>
        <p class="mt-5 max-w-xl text-lg leading-relaxed">El contenido puede haber cambiado o ya no estar disponible. También puede que los filtros no sean válidos.</p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('businesses.index', ['city' => 'playa-del-carmen']) }}" class="inline-flex min-h-12 max-w-full items-center justify-center rounded-xl border border-brand-text bg-brand-action px-5 py-3 text-center font-bold text-brand-text hover:underline">Ver negocios</a>
            <a href="{{ route('bootstrap') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl px-5 py-3 font-semibold underline underline-offset-4">Volver al inicio</a>
        </div>
    </section>
</x-public-layout>
