<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GeoAI | Choose Location</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_24%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
                <header class="flex items-center justify-between rounded-full border border-slate-200 bg-white/85 px-4 py-3 shadow-sm backdrop-blur sm:px-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 text-base font-semibold text-slate-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-500 text-white shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 18L8.5 13.5L12 16L20 8" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M20 8H14" stroke-linecap="round" />
                                <path d="M20 8V2" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span>GeoAI</span>
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="text-sm font-medium text-slate-500">Step 2 of 2</div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">Log out</button>
                        </form>
                    </div>
                </header>

                <main class="flex flex-1 items-center justify-center py-8 sm:py-10 lg:py-14">
                    <section class="w-full rounded-[32px] border border-slate-200 bg-white/90 p-6 shadow-[0_24px_100px_rgba(15,23,42,0.08)] backdrop-blur sm:p-8 lg:p-10">
                        <div class="mx-auto max-w-5xl">
                            <div class="text-center">
                                <div class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-sm font-medium text-cyan-700">
                                    Choose the area for your new location
                                </div>
                                <h1 class="mt-5 text-3xl font-semibold leading-tight text-slate-950 sm:text-4xl">
                                    Where do you want to open your business?
                                </h1>
                                <p class="mx-auto mt-4 max-w-2xl text-lg leading-8 text-slate-600">
                                    Select a preferred region to see the best site options for your chosen business type.
                                </p>
                            </div>

                            <div class="mt-10 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
                                <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-500">Location input</p>
                                            <p class="text-lg font-semibold text-slate-950">Enter your preferred area in Malaysia</p>
                                        </div>
                                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-sm font-medium text-cyan-700">Malaysia only</span>
                                    </div>

                                    <form id="analysis-form" action="{{ route('analysis.store') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="business_type" value="{{ request('business_type') ?? 'general' }}">
                                        <div>
                                            <label for="location" class="mb-2 block text-sm font-semibold text-slate-700">Business location</label>
                                            <div class="flex flex-col gap-3 sm:flex-row">
                                                <input
                                                    id="location"
                                                    name="location"
                                                    type="text"
                                                    placeholder="e.g. Petaling Jaya, Selangor"
                                                    class="w-full rounded-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none ring-0 transition focus:border-cyan-500"
                                                    required
                                                >
                                                <button id="analysis-submit" type="submit" class="rounded-full bg-cyan-500 px-6 py-3 font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400">
                                                    <span id="analysis-submit-label">Confirm</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="radius" class="mb-2 block text-sm font-semibold text-slate-700">Analysis range</label>
                                            <select id="radius" name="radius" class="w-full rounded-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-500" required>
                                                <option value="500">500 meters</option>
                                                <option value="1500">1.5 kilometers</option>
                                                <option value="3000">3 kilometers</option>
                                            </select>
                                        </div>
                                    </form>

                                    <div class="mt-6">
                                        <p class="mb-3 text-sm font-semibold text-slate-500">Suggested target areas</p>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm">
                                                <p class="text-base font-semibold text-slate-900">Kuala Lumpur</p>
                                                <p class="mt-1 text-sm text-slate-600">High density and strong demand</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm">
                                                <p class="text-base font-semibold text-slate-900">Subang Jaya</p>
                                                <p class="mt-1 text-sm text-slate-600">Fast-growing suburban hub</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm">
                                                <p class="text-base font-semibold text-slate-900">Johor Bahru</p>
                                                <p class="mt-1 text-sm text-slate-600">Strong commercial movement</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm">
                                                <p class="text-base font-semibold text-slate-900">Penang</p>
                                                <p class="mt-1 text-sm text-slate-600">Excellent consumer traffic</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-500">Selected business</p>
                                    <p class="mt-2 text-xl font-semibold text-slate-950">{{ request('business_type') ? str_replace('-', ' ', ucfirst(request('business_type'))) : 'Your selection' }}</p>

                                    <div class="mt-6 rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
                                        <p class="text-sm font-medium text-cyan-700">Why this works</p>
                                        <p class="mt-2 text-sm leading-7 text-slate-700">
                                            The best locations are usually near dense residential areas, strong commuting routes, and high daily foot traffic.
                                        </p>
                                    </div>

                                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                        <a href="{{ route('analysis.business') }}" class="rounded-full border border-slate-300 px-5 py-3 text-center font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
        <div id="analysis-loading" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 px-6 backdrop-blur-sm" role="status" aria-live="polite" aria-label="Analyzing location">
            <div class="w-full max-w-lg rounded-3xl border border-cyan-100 bg-white p-10 text-center shadow-2xl sm:p-12">
                <div class="analysis-loading-spinner mx-auto h-20 w-20 rounded-full border-8 border-cyan-100 border-t-cyan-500"></div>
                <p class="mt-7 text-2xl font-semibold text-slate-950">Analyzing your location</p>
                <p class="mx-auto mt-3 max-w-md text-base leading-7 text-slate-600">GeoAI is checking demographics, transport, affordability, and nearby opportunities.</p>
                <div class="mx-auto mt-7 h-2 max-w-sm overflow-hidden rounded-full bg-cyan-100">
                    <div class="analysis-loading-progress h-full w-1/2 rounded-full bg-cyan-500"></div>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('analysis-form').addEventListener('submit', () => {
                document.getElementById('analysis-loading').classList.remove('hidden');
                document.getElementById('analysis-loading').classList.add('flex');
                document.getElementById('analysis-submit').disabled = true;
                document.getElementById('analysis-submit-label').textContent = 'Analyzing...';
            });
        </script>
        <style>
            .analysis-loading-spinner { animation: analysis-spin 0.9s linear infinite; }
            .analysis-loading-progress { animation: analysis-progress 1.8s ease-in-out infinite; }
            @keyframes analysis-spin { to { transform: rotate(360deg); } }
            @keyframes analysis-progress { 0%, 100% { transform: translateX(-100%); } 50% { transform: translateX(100%); } }
        </style>
    </body>
</html>
