<x-public-layout title="Límite de intentos · Tu Gurú Local" :noindex="true">
    <section class="mx-auto max-w-2xl px-5 py-12 sm:px-8 sm:py-16">
        <h1 class="font-display text-3xl font-extrabold">Se alcanzó el límite de intentos</h1>
        <p class="mt-5 leading-relaxed">Espera antes de volver a enviar el formulario. Limitamos los envíos para evitar el spam.</p>
        @if($seconds = $retryAfter)
            <p class="mt-3 text-brand-muted">Podrás intentarlo de nuevo en {{ max(1, (int) ceil((int) $seconds / 60)) }} minutos.</p>
        @endif
        <a href="{{ route('bootstrap') }}" class="mt-6 inline-flex min-h-12 items-center rounded-xl bg-brand-action px-6 py-3 font-bold text-brand-text">Volver a Inicio</a>
    </section>
</x-public-layout>
