<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Tu Gurú Local · Proyecto en preparación</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-brand-page font-sans text-brand-text">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center px-6 py-16">
        <section class="w-full rounded-3xl bg-brand-deep px-8 py-12 text-white shadow-xl sm:px-12" aria-labelledby="page-title">
            <p class="mb-5 text-sm font-bold tracking-[0.2em] uppercase text-brand-action">Playa del Carmen</p>
            <h1 id="page-title" class="font-display text-4xl font-extrabold leading-tight sm:text-5xl">Tu Gurú Local</h1>
            <p class="mt-6 max-w-prose text-lg leading-relaxed text-white/90">Estamos preparando una guía local con información verificada. El contenido público estará disponible cuando termine la revisión editorial.</p>
        </section>
    </main>
</body>
</html>
