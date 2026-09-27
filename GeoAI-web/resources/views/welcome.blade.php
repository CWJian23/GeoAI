<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="GeoAI helps teams find high-potential business locations using geospatial intelligence and AI-powered analysis.">
        <title>GeoAI | Bright Analysis Hub</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_28%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
                <header class="flex items-center justify-between rounded-full border border-slate-200 bg-white/80 px-4 py-3 shadow-sm backdrop-blur sm:px-6">
                    <a href="#" class="flex items-center gap-3 text-base font-semibold text-slate-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-500 text-white shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 18L8.5 13.5L12 16L20 8" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M20 8H14" stroke-linecap="round" />
                                <path d="M20 8V2" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span>GeoAI</span>
                    </a>

                    <div class="flex items-center gap-3">
                        @auth
                            <span class="text-sm font-semibold text-slate-700">Hi, {{ auth()->user()->name }}</span>
                            <button type="button" id="open-account-menu" aria-label="Open account menu" aria-controls="account-sidebar" aria-expanded="false" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" /></svg>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Start Analysis</a>
                        @endauth
                    </div>
                </header>

                <main class="flex flex-1 items-center justify-center py-8 sm:py-10 lg:py-14">
                    <section class="w-full rounded-[32px] border border-slate-200 bg-white/90 p-8 shadow-[0_24px_100px_rgba(15,23,42,0.08)] backdrop-blur sm:p-10 lg:p-14">
                        <div class="mx-auto max-w-3xl text-center">
                            <div class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-sm font-medium text-cyan-700">
                                Bright, clean, and ready for action
                            </div>

                            <h1 class="mt-6 text-4xl font-semibold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                                Start your location analysis in one click.
                            </h1>

                            <p class="mt-5 text-lg leading-8 text-slate-600">
                                Explore markets, compare opportunities, and make confident decisions with a simple, modern workspace built for speed.
                            </p>

                            <div class="mt-10 flex justify-center">
                                <a id="analysis" href="{{ auth()->check() ? route('analysis.business') : route('login') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-500 px-8 py-4 text-lg font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400">
                                    Start Analysis
                                </a>
                            </div>
                        </div>

                        <div class="mt-10 grid gap-4 md:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left">
                                <p class="text-2xl font-semibold text-slate-950">3x</p>
                                <p class="mt-1 text-sm text-slate-600">Faster location screening</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left">
                                <p class="text-2xl font-semibold text-slate-950">94%</p>
                                <p class="mt-1 text-sm text-slate-600">Decision confidence</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left">
                                <p class="text-2xl font-semibold text-slate-950">24/7</p>
                                <p class="mt-1 text-sm text-slate-600">Live market signals</p>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        @auth
            <div id="account-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/30"></div>
            <aside id="account-sidebar" aria-hidden="true" class="fixed right-0 top-0 z-50 flex h-full w-full max-w-sm translate-x-full flex-col border-l border-slate-200 bg-white p-6 shadow-2xl transition-transform duration-300 ease-out pointer-events-none">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700">Account menu</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-950">Hi, {{ auth()->user()->name }}</h2>
                    </div>
                    <button type="button" id="close-account-menu" aria-label="Close account menu" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-300 text-xl text-slate-600">&times;</button>
                </div>

                <nav class="mt-8 space-y-3">
                    <a href="{{ route('analysis.history') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4 font-semibold text-slate-800 transition hover:border-cyan-400 hover:bg-cyan-50">
                        <span>Previous analysis</span>
                        <span aria-hidden="true" class="text-cyan-700">&rarr;</span>
                    </a>
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-slate-300 px-4 py-3 font-semibold text-slate-700 transition hover:border-red-300 hover:text-red-700">Log out</button>
                </form>
            </aside>

            <script>
                const accountSidebar = document.getElementById('account-sidebar');
                const accountBackdrop = document.getElementById('account-backdrop');
                const openAccountMenu = document.getElementById('open-account-menu');
                const closeAccountMenu = document.getElementById('close-account-menu');

                const setAccountMenuOpen = (isOpen) => {
                    accountSidebar.classList.toggle('translate-x-full', !isOpen);
                    accountSidebar.classList.toggle('pointer-events-none', !isOpen);
                    accountBackdrop.classList.toggle('hidden', !isOpen);
                    accountSidebar.setAttribute('aria-hidden', String(!isOpen));
                    openAccountMenu.setAttribute('aria-expanded', String(isOpen));

                    if (isOpen) {
                        closeAccountMenu.focus();
                    } else {
                        openAccountMenu.focus();
                    }
                };

                openAccountMenu.addEventListener('click', () => setAccountMenuOpen(true));
                closeAccountMenu.addEventListener('click', () => setAccountMenuOpen(false));
                accountBackdrop.addEventListener('click', () => setAccountMenuOpen(false));
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && openAccountMenu.getAttribute('aria-expanded') === 'true') {
                        setAccountMenuOpen(false);
                    }
                });
            </script>
        @endauth

    </body>
</html>

