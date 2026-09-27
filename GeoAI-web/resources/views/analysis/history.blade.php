<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GeoAI | Analysis History</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_28%)]">
            <div class="mx-auto min-h-screen max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between rounded-full border border-slate-200 bg-white/85 px-4 py-3 shadow-sm backdrop-blur sm:px-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-base font-semibold text-slate-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-500 text-white">GeoAI</span>
                        <span>Analysis History</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-300 hover:text-red-700">Log out</button>
                    </form>
                </header>

                <main class="py-10 sm:py-14">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700">{{ auth()->user()->name }}</p>
                            <h1 class="mt-2 text-3xl font-semibold text-slate-950 sm:text-4xl">Previous analyses</h1>
                            <p class="mt-3 text-slate-600">Review the analysis results saved to your account.</p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('home') }}" class="rounded-full border border-slate-300 px-5 py-3 text-center font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">Back</a>
                            <a href="{{ route('analysis.business') }}" class="rounded-full bg-cyan-500 px-5 py-3 text-center font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400">Start Analysis</a>
                        </div>
                    </div>

                    <section class="mt-8 space-y-4">
                        @forelse ($analysisRecords as $record)
                            <a href="{{ route('analysis.result', ['id' => $record->id]) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-cyan-400 hover:shadow-md">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-950">{{ ucwords(str_replace('-', ' ', $record->business_type)) }}</h2>
                                        <p class="mt-1 text-slate-600">{{ $record->location }}</p>
                                        <p class="mt-2 text-sm text-slate-500">{{ $record->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-cyan-700">View result &rarr;</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                                <h2 class="text-xl font-semibold text-slate-900">No previous analyses yet.</h2>
                                <p class="mt-2 text-slate-600">Start an analysis to see your saved results here.</p>
                                <a href="{{ route('analysis.business') }}" class="mt-6 inline-block rounded-full bg-cyan-500 px-5 py-3 font-semibold text-white transition hover:bg-cyan-400">Start Analysis</a>
                            </div>
                        @endforelse
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
