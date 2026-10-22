@props(['title', 'description' => null, 'canonical' => null, 'noindex' => false, 'socialImage' => null])
<!doctype html>
<html lang="es" data-theme="light">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if($description)<meta name="description" content="{{ $description }}">
@endif

    @if($noindex)<meta name="robots" content="noindex, nofollow">
@endif

    @if($canonical)<link rel="canonical" href="{{ $canonical }}">
@endif

    <meta property="og:type" content="website"><meta property="og:locale" content="es_MX">
    <meta property="og:site_name" content="Tu Gurú Local"><meta property="og:title" content="{{ $title }}">
    @if($description)<meta property="og:description" content="{{ $description }}">
@endif

    @if($canonical)<meta property="og:url" content="{{ $canonical }}">
@endif

    @if($socialImage)<meta property="og:image" content="{{ $socialImage }}">
@endif

    <meta name="twitter:card" content="{{ $socialImage ? 'summary_large_image' : 'summary' }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-brand-page font-sans text-brand-text antialiased">
    <a href="#contenido" class="sr-only fixed top-3 left-3 z-50 rounded-xl bg-brand-action px-5 py-3 font-bold text-brand-text focus:not-sr-only">Saltar al contenido</a>
    <header class="bg-brand-header text-white"><div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-5 sm:px-8">
        <a href="{{ route('bootstrap') }}" class="rounded-lg font-display text-xl font-extrabold tracking-tight">Tu Gurú <span class="text-brand-action">Local</span></a>
        <span class="text-sm text-white/85">Playa del Carmen</span>
    </div></header>
    <main id="contenido" tabindex="-1" class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">{{ $slot }}</main>
    <footer class="mt-12 bg-brand-deep text-white"><div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-10 sm:px-8"><p class="font-display text-xl font-extrabold">Tu Gurú Local</p><p class="max-w-xl text-sm leading-relaxed text-white/80">Información útil para descubrir Playa del Carmen.</p><p class="text-sm text-white/70">© {{ date('Y') }} Tu Gurú Local</p></div></footer>
</body>
</html>
