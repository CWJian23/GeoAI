<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GeoAI | Choose Business Type</title>
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
                        <div class="text-sm font-medium text-slate-500">Step 1 of 2</div>
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
                                    Select the business you want to evaluate
                                </div>
                                <h1 class="mt-5 text-3xl font-semibold leading-tight text-slate-950 sm:text-4xl">
                                    What kind of business are you planning to open?
                                </h1>
                                <p class="mx-auto mt-4 max-w-2xl text-lg leading-8 text-slate-600">
                                    Pick a business type to receive a tailored market fit and location recommendation.
                                </p>
                            </div>

                            <form action="{{ route('analysis.location') }}" method="GET" class="mt-10">
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @php
                                        $businesses = [
                                            ['value' => 'food-beverage', 'label' => 'Food & Beverage', 'icon' => '🍽️'],
                                            ['value' => 'fitness', 'label' => 'Fitness', 'icon' => '💪'],
                                            ['value' => 'convenience-shop', 'label' => 'Convenience Shop', 'icon' => '🛒'],
                                            ['value' => 'beauty-cosmetic', 'label' => 'Beauty Cosmetic', 'icon' => '💄'],
                                            ['value' => 'pets-store', 'label' => 'Pets Store', 'icon' => '🐾'],
                                            ['value' => 'bakery', 'label' => 'Bakery', 'icon' => '🥐'],
                                            ['value' => 'book-store', 'label' => 'Book Store', 'icon' => '📚'],
                                            ['value' => 'car-accessories', 'label' => 'Car Accessories', 'icon' => '🚗'],
                                        ];
                                    @endphp

                                    @foreach ($businesses as $business)
                                        <label class="group flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-cyan-400 hover:bg-cyan-50">
                                            <input type="radio" name="business_type" value="{{ $business['value'] }}" class="h-4 w-4 border-slate-300 text-cyan-500 focus:ring-cyan-500" required>
                                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">{{ $business['icon'] }}</span>
                                            <span class="text-base font-semibold text-slate-800">{{ $business['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                                    <a href="{{ url('/') }}" class="rounded-full border border-slate-300 px-6 py-3 font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">
                                        Back
                                    </a>
                                    <button type="submit" class="rounded-full bg-cyan-500 px-8 py-3 font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400">
                                        Continue to Location
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
