<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="GeoAI helps teams find high-potential business locations using geospatial intelligence and AI-powered analysis.">
        <title>GeoAI | Business Site Selection</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 -z-10 h-[40rem] bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_38%),radial-gradient(circle_at_82%_0%,_rgba(16,185,129,0.16),_transparent_32%)]"></div>

            <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="#" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-slate-900">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-600">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 18L8.5 13.5L12 16L20 8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 8H14" stroke-linecap="round" />
                            <path d="M20 8V2" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span>GeoAI</span>
                </a>

                <nav class="hidden items-center gap-6 text-sm text-slate-600 md:flex">
                    <a href="#platform" class="transition hover:text-slate-950">Platform</a>
                    <a href="#workflow" class="transition hover:text-slate-950">Workflow</a>
                    <a href="#contact" class="transition hover:text-slate-950">Contact</a>
                </nav>
            </header>

            <main class="mx-auto max-w-7xl px-6 pb-20 pt-4 lg:px-8 lg:pt-8">
                <section class="grid items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                    <div>
                        <div class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-sm font-medium text-cyan-700">
                            Business site selection, reimagined
                        </div>

                        <h1 class="mt-6 text-4xl font-semibold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Choose the next best location with spatial intelligence.
                        </h1>

                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                            GeoAI helps expansion teams evaluate demand, accessibility, risk, and opportunity in one clear decision platform.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-4">
                            <a href="#contact" class="rounded-full bg-slate-950 px-6 py-3 font-semibold text-white transition hover:bg-slate-800">
                                Request a demo
                            </a>
                            <a href="#workflow" class="rounded-full border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 transition hover:border-cyan-500 hover:text-cyan-700">
                                See the workflow
                            </a>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                                <div class="text-2xl font-semibold text-slate-950">3x</div>
                                <div class="mt-1 text-sm text-slate-500">Faster screening</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                                <div class="text-2xl font-semibold text-slate-950">94%</div>
                                <div class="mt-1 text-sm text-slate-500">Decision confidence</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                                <div class="text-2xl font-semibold text-slate-950">24/7</div>
                                <div class="mt-1 text-sm text-slate-500">Live signals</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_20px_80px_rgba(15,23,42,0.08)]">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-slate-400">Opportunity score</p>
                                    <p class="mt-2 text-3xl font-semibold">88.4</p>
                                </div>
                                <div class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-sm text-emerald-300">
                                    High fit
                                </div>
                            </div>

                            <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-950/70 p-4">
                                <div class="mb-4 flex items-center justify-between text-sm text-slate-400">
                                    <span>Market map</span>
                                    <span>North hub • 2.4 km</span>
                                </div>
                                <svg viewBox="0 0 320 220" class="h-56 w-full rounded-xl bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.16),_transparent_55%)]">
                                    <path d="M40 180C70 140 100 120 132 118C164 116 190 138 214 132C244 124 274 94 292 60" stroke="#38bdf8" stroke-width="3" fill="none" stroke-linecap="round" />
                                    <circle cx="112" cy="122" r="9" fill="#f8fafc" />
                                    <circle cx="202" cy="132" r="10" fill="#22d3ee" />
                                    <circle cx="270" cy="90" r="7" fill="#f8fafc" />
                                    <circle cx="86" cy="152" r="6" fill="#34d399" />
                                </svg>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-3">
                                    <p class="text-sm text-slate-400">Foot traffic</p>
                                    <p class="mt-1 text-lg font-semibold">+18.2%</p>
                                </div>
                                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-3">
                                    <p class="text-sm text-slate-400">Competition</p>
                                    <p class="mt-1 text-lg font-semibold">Balanced</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="platform" class="mt-24 grid gap-6 lg:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">01</div>
                        <h2 class="mt-3 text-xl font-semibold text-slate-950">Unify your geospatial layers</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Bring location, mobility, accessibility, and commercial context into one shared view.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">02</div>
                        <h2 class="mt-3 text-xl font-semibold text-slate-950">Score sites with explainable AI</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Compare candidate locations using transparent criteria that leadership can trust and act on.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-700">03</div>
                        <h2 class="mt-3 text-xl font-semibold text-slate-950">Present a clear recommendation</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Turn analysis into concise, executive-ready briefings for stakeholders and partners.</p>
                    </div>
                </section>

                <section id="workflow" class="mt-24 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.06)] lg:p-10">
                    <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">How it works</p>
                            <h2 class="mt-3 text-3xl font-semibold text-slate-950 sm:text-4xl">From raw data to confident site decisions in a few clicks.</h2>
                            <div class="mt-8 space-y-4">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="font-semibold text-slate-950">1. Define your expansion criteria</div>
                                    <p class="mt-2 text-sm text-slate-600">Set your catchment, budget, accessibility, and growth objectives.</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="font-semibold text-slate-950">2. Let GeoAI score each location</div>
                                    <p class="mt-2 text-sm text-slate-600">The platform blends location, mobility, and market signals into a ranked shortlist.</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="font-semibold text-slate-950">3. Share a clear recommendation</div>
                                    <p class="mt-2 text-sm text-slate-600">Deliver visuals, rationale, and action steps to your team in one view.</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-6">
                            <p class="text-sm text-slate-500">Recommended shortlist</p>
                            <div class="mt-4 space-y-4">
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-slate-950">North Harbor</span>
                                        <span class="text-emerald-600">92</span>
                                    </div>
                                    <div class="mt-3 h-2 rounded-full bg-slate-200">
                                        <div class="h-2 w-[92%] rounded-full bg-emerald-500"></div>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-slate-950">River Point</span>
                                        <span class="text-cyan-700">87</span>
                                    </div>
                                    <div class="mt-3 h-2 rounded-full bg-slate-200">
                                        <div class="h-2 w-[87%] rounded-full bg-cyan-500"></div>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-slate-950">West Market</span>
                                        <span class="text-slate-600">74</span>
                                    </div>
                                    <div class="mt-3 h-2 rounded-full bg-slate-200">
                                        <div class="h-2 w-[74%] rounded-full bg-slate-400"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="contact" class="mt-24 rounded-[2rem] border border-slate-200 bg-slate-950 p-8 text-center text-white lg:p-12">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Ready to plan your next location?</p>
                    <h2 class="mt-3 text-3xl font-semibold sm:text-4xl">Let’s build a smarter expansion strategy together.</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">GeoAI is designed for retail, hospitality, logistics, and other teams that need to choose locations with confidence.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="mailto:hello@geoai.example" class="rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition hover:bg-cyan-300">
                            hello@geoai.example
                        </a>
                        <a href="#" class="rounded-full border border-slate-700 px-6 py-3 font-semibold text-white transition hover:border-cyan-400 hover:text-cyan-300">
                            Request a walkthrough
                        </a>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 px-6 py-6 text-center text-sm text-slate-500 lg:px-8">
                GeoAI © 2026 · Built for modern site selection teams.
            </footer>
        </div>
    </body>
</html>
