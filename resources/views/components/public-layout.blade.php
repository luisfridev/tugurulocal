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
    <meta name="twitter:title" content="{{ $title }}">
    @if($description)<meta name="twitter:description" content="{{ $description }}">@endif
    @if($socialImage)<meta name="twitter:image" content="{{ $socialImage }}">@endif
    @vite(['resources/css/app.css','resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-brand-page font-sans text-brand-text wrap-anywhere antialiased">
    <a href="#contenido" class="sr-only fixed top-3 left-3 z-50 max-w-[calc(100%_-_1.5rem)] rounded-xl border border-brand-text bg-brand-action px-5 py-3 font-bold text-brand-text focus:not-sr-only">Saltar al contenido</a>
    <header class="sticky top-0 z-40 border-b border-white/15 bg-brand-header/95 text-white backdrop-blur-[8px]">
        <div class="mx-auto flex min-h-16 max-w-6xl items-center justify-between gap-4 px-5 sm:px-8">
            <a href="{{ route('bootstrap') }}" class="inline-flex min-h-11 max-w-full items-center whitespace-nowrap rounded-lg font-display text-xl font-extrabold tracking-tight">Tu Gurú <span class="ml-1 text-brand-action">Local</span></a>
        </div>
    </header>
    <nav aria-label="Navegación principal" class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-3 border-t border-white/15 bg-brand-header pb-[env(safe-area-inset-bottom)] text-white md:inset-x-auto md:bottom-auto md:right-[max(2rem,calc((100vw-72rem)/2))] md:top-0 md:z-50 md:flex md:h-16 md:border-0 md:bg-transparent md:pb-0">
        <a href="{{ route('cities.show', ['city' => 'playa-del-carmen']) }}" @if(request()->routeIs('cities.show')) aria-current="page" @endif class="public-nav-link relative flex min-h-[60px] min-w-0 items-center justify-center whitespace-nowrap px-5 py-3 text-sm font-semibold hover:bg-white/10 active:bg-white/20 md:min-h-11 md:rounded-xl md:text-base">Inicio</a>
        <a href="{{ route('businesses.index', ['city' => 'playa-del-carmen']) }}" @if(request()->routeIs('businesses.index', 'categories.show', 'areas.show')) aria-current="page" @endif class="public-nav-link relative flex min-h-[60px] min-w-0 items-center justify-center whitespace-nowrap px-5 py-3 text-sm font-semibold hover:bg-white/10 active:bg-white/20 md:min-h-11 md:rounded-xl md:text-base">Negocios</a>
        <a href="{{ route('submissions.create') }}" @if(request()->routeIs('submissions.*')) aria-current="page" @endif class="public-nav-link relative flex min-h-[60px] min-w-0 items-center justify-center px-3 py-3 text-sm font-semibold hover:bg-white/10 active:bg-white/20 md:min-h-11 md:rounded-xl md:px-5 md:text-base">Recomendar</a>
    </nav>
    <main id="contenido" tabindex="-1" class="min-w-0">{{ $slot }}</main>
    <footer class="bg-brand-deep text-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-5 py-10 pb-[calc(5rem+env(safe-area-inset-bottom))] sm:px-8 md:grid-cols-[minmax(0,1fr)_auto] md:items-end md:pb-10">
            <div class="grid gap-3">
                <p class="font-display text-xl font-extrabold">Tu Gurú Local</p>
                <p class="max-w-xl text-sm leading-relaxed text-white/90">Información útil para descubrir Playa del Carmen.</p>
                <p class="text-sm text-white/85">© {{ date('Y') }} Tu Gurú Local</p>
            </div>
            <nav aria-label="Navegación del pie" class="grid gap-2 sm:grid-cols-2 sm:gap-x-8">
                <a href="{{ route('cities.show', ['city' => 'playa-del-carmen']) }}" class="inline-flex min-h-11 items-center rounded-lg font-semibold underline underline-offset-4 hover:text-brand-action">Inicio</a>
                <a href="{{ route('businesses.index', ['city' => 'playa-del-carmen']) }}" class="inline-flex min-h-11 items-center rounded-lg font-semibold underline underline-offset-4 hover:text-brand-action">Negocios</a>
            </nav>
        </div>
    </footer>
</body>
</html>
