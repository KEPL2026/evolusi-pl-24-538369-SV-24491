<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Daily Sync')</title>

    <script>
        (() => {
            try {
                const stored = localStorage.getItem('daily-sync.theme');
                const dark = stored === null
                    ? window.matchMedia('(prefers-color-scheme: dark)').matches
                    : stored === 'dark';
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col antialiased">

    @php
        $navLinks = collect([
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Pomodoro', 'route' => 'pomodoro.index'],
            ['label' => 'Habits', 'route' => 'habits.index'],
            ['label' => 'Notes', 'route' => 'notes.index'],
        ])->filter(fn ($link) => Route::has($link['route']));
    @endphp

    <header class="sticky top-0 z-50 px-3 pt-3 sm:px-4 sm:pt-4">
        <div class="nav-card mx-auto flex w-full max-w-[1120px] items-center justify-between gap-4 rounded-[24px] px-4 py-3 sm:px-5">
            <a href="{{ route('dashboard') }}"
               class="-rotate-[1.5deg] inline-flex items-center gap-2.5 rounded-[18px] border-[3px] border-ink bg-paper-raised px-3 py-1.5 shadow-[3px_3px_0_0_var(--ds-shadow)] transition-transform hover:rotate-0 sm:px-4">
                <svg class="h-6 w-6 text-coral" fill="none" stroke="currentColor" stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2.6" viewBox="0 0 24 24">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.19"/>
                </svg>
                <span class="font-display text-xl font-semibold tracking-wide text-ink sm:text-[22px]">Daily Sync</span>
            </a>

            <nav class="hidden items-center gap-2 md:flex">
                @foreach ($navLinks as $link)
                    @php $active = request()->routeIs($link['route']); @endphp
                    <a href="{{ route($link['route']) }}"
                       @class([
                           'rounded-full px-4 py-1.5 text-[15px] transition-all',
                           'font-display font-semibold',
                           $active
                               ? 'border-[3px] border-ink bg-yellow text-ink-const shadow-[3px_3px_0_0_var(--ds-shadow)]'
                               : 'border-[3px] border-transparent font-body font-bold text-ink hover:border-ink hover:bg-paper',
                       ])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2.5">
                <button id="audio-toggle" type="button" title="Toggle suara notifikasi"
                        class="btn-stamp bg-paper-raised px-3 py-1.5 text-ink hover:bg-paper">
                    <span id="audio-icon" class="material-symbols-outlined ms-fill text-green-dark text-lg">volume_up</span>
                    <span id="audio-label" class="hidden font-display text-[13px] font-semibold sm:inline">Audio On</span>
                </button>

                <button id="theme-toggle" type="button" title="Ganti tema terang/gelap" aria-label="Ganti tema" aria-pressed="false"
                        class="btn-stamp gap-1.5 bg-yellow px-3 py-1.5 text-ink-const dark:bg-berry dark:text-white">
                    <span id="theme-icon" class="material-symbols-outlined ms-fill text-[18px]">light_mode</span>
                    <span id="theme-label" class="hidden font-display text-[13px] font-semibold sm:inline">Terang</span>
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="mx-auto mt-16 w-full max-w-[1120px] px-4 pb-8 pt-8 sm:px-6">
        <div class="flex flex-col items-center justify-between gap-3 border-t-[3px] border-dashed border-ink/25 pt-6 sm:flex-row">
            <div class="flex flex-col items-center gap-1 text-center sm:flex-row sm:gap-2 sm:text-left">
                <span class="font-display text-lg font-semibold text-ink">Daily Sync</span>
                <span class="hidden text-ink-soft sm:inline">•</span>
                <span class="text-sm text-ink-soft">Daily Sync Productivity Suite • Stay Focused, Stamp Habits!</span>
            </div>
            <span class="font-display text-[13px] font-semibold text-ink-soft">v0.1 • Fondasi UI</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
