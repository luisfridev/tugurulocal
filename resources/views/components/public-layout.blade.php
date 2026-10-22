@props(['title', 'description' => null, 'canonical' => null, 'noindex' => false, 'socialImage' => null, 'robotsFollow' => false])
<!doctype html>
<html lang="es" data-theme="light">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if($description)<meta name="description" content="{{ $description }}">
@endif

    @if($noindex)<meta name="robots" content="noindex, {{ $robotsFollow ? 'follow' : 'nofollow' }}">
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
<body class="min-h-screen bg-brand-page font-sans text-brand-text wrap-anywhere antialiased">
    <a href="#contenido" class="sr-only fixed top-3 left-3 z-50 max-w-[calc(100%_-_1.5rem)] rounded-xl border border-brand-text bg-brand-action px-5 py-3 font-bold text-brand-text focus:not-sr-only">Saltar al contenido</a>
    <header class="bg-brand-header text-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-5 pt-3 sm:px-8 md:flex-row md:items-center md:justify-between md:gap-6 md:py-3">
            <div class="flex min-w-0 flex-wrap items-center justify-between gap-x-5 gap-y-1">
                <a href="{{ route('bootstrap') }}" class="inline-flex min-h-11 max-w-full flex-wrap items-center rounded-lg font-display text-xl font-extrabold tracking-tight">Tu Gurú <span class="ml-1 text-brand-action">Local</span></a>
                <span class="text-sm text-white/85 md:hidden">Playa del Carmen</span>
            </div>
            <nav aria-label="Navegación principal" class="grid min-w-0 grid-cols-1 gap-2 min-[20rem]:grid-cols-2 md:flex">
                <a href="{{ route('cities.show', ['city' => 'playa-del-carmen']) }}" @if(request()->routeIs('cities.show')) aria-current="page" @endif class="flex min-h-12 min-w-0 items-center justify-center rounded-t-xl px-3 py-3 font-semibold hover:bg-white/10 active:bg-white/20 md:rounded-xl md:px-5 {{ request()->routeIs('cities.show') ? 'bg-brand-deep underline decoration-brand-action decoration-2 underline-offset-8' : '' }}">Inicio</a>
                <a href="{{ route('businesses.index', ['city' => 'playa-del-carmen']) }}" @if(request()->routeIs('businesses.index', 'categories.show', 'areas.show')) aria-current="page" @endif class="flex min-h-12 min-w-0 items-center justify-center rounded-t-xl px-3 py-3 font-semibold hover:bg-white/10 active:bg-white/20 md:rounded-xl md:px-5 {{ request()->routeIs('businesses.index', 'categories.show', 'areas.show') ? 'bg-brand-deep underline decoration-brand-action decoration-2 underline-offset-8' : '' }}">Negocios</a>
            </nav>
        </div>
    </header>
    <main id="contenido" tabindex="-1" class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">{{ $slot }}</main>
    <footer class="mt-12 bg-brand-deep text-white"><div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-10 sm:px-8"><p class="font-display text-xl font-extrabold">Tu Gurú Local</p><p class="max-w-xl text-sm leading-relaxed text-white/80">Información útil para descubrir Playa del Carmen.</p><p class="text-sm text-white/70">© {{ date('Y') }} Tu Gurú Local</p></div></footer>
</body>
</html>
