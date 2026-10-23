<x-public-layout title="Sesión vencida · Tu Gurú Local" :noindex="true">
    <section class="mx-auto max-w-2xl px-5 py-12 sm:px-8 sm:py-16">
        <h1 class="font-display text-3xl font-extrabold">Tu sesión venció</h1>
        <p class="mt-5 leading-relaxed">Abre de nuevo el formulario para enviarlo. No recibimos los datos de este intento.</p>
        <a href="{{ request()->routeIs('listings.report.store') ? route('listings.report', ['listing' => request()->route('listing')]) : route('submissions.create') }}" class="mt-6 inline-flex min-h-12 items-center rounded-xl bg-brand-action px-6 py-3 font-bold text-brand-text">Abrir el formulario</a>
    </section>
</x-public-layout>
