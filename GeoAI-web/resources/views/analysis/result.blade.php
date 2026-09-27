<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GeoAI | Recommendation</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_24%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-4 sm:px-6 lg:px-8 lg:py-6">
                <header class="relative z-10 flex items-center justify-between rounded-full border border-slate-200 bg-white/85 px-4 py-3 shadow-sm backdrop-blur sm:px-6">
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
                        <div class="text-sm font-medium text-slate-500">Result</div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">Log out</button>
                        </form>
                    </div>
                </header>

                <main class="flex flex-1 items-center justify-center py-8 sm:py-10 lg:py-14">
                    <section class="w-full rounded-[32px] border border-slate-200 bg-white/90 p-5 shadow-[0_24px_100px_rgba(15,23,42,0.08)]">
                        @php
                            $analysisRadius = (int) ($geo_ai['radius_meters'] ?? 500);
                        @endphp
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Recommendation</p>
                                <h1 class="mt-2 text-3xl font-semibold text-slate-950">{{ ucwords(str_replace('-', ' ', $business_type ?? 'Business')) }} opportunity</h1>
                                <p class="mt-2 text-sm text-slate-600">Location: <span class="font-semibold text-slate-900">{{ $location }}</span> <span class="text-slate-400">(within {{ $analysisRadius >= 1000 ? ($analysisRadius / 1000) . ' km' : $analysisRadius . ' m' }})</span></p>
                            </div>
                            <a href="{{ route('analysis.location', ['business_type' => $business_type]) }}" class="rounded-full border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700">Back</a>
                        </div>

                        <div class="mt-6 grid min-w-0 gap-6 overflow-hidden lg:grid-cols-[minmax(0,1.55fr)_240px]">
                            <div class="relative z-0 min-w-0 max-w-full isolate overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                <div class="border-b border-slate-200 bg-white px-4 py-3">
                                    <p class="font-semibold text-slate-900">{{ $location }} map</p>
                                    <p class="mt-1 text-sm text-slate-500">Red circle: analysis range around the searched location. Green markers: suggested areas.</p>
                                </div>
                                <div id="analysis-map" class="h-[300px] w-full sm:h-[380px]"></div>
                            </div>

                            @php
                                $score = max(0, min(100, (int) $availability_score));
                                $scoreIsHigh = $score > 80;
                                $scoreIsMedium = $score >= 50 && $score <= 80;
                                $scorePanelClass = $scoreIsHigh ? 'border-emerald-200 bg-emerald-100' : ($scoreIsMedium ? 'border-amber-200 bg-amber-100' : 'border-red-200 bg-red-100');
                                $scoreTextClass = $scoreIsHigh ? 'text-emerald-800' : ($scoreIsMedium ? 'text-amber-800' : 'text-red-800');
                                $scoreLabel = $scoreIsHigh ? 'Good availability' : ($scoreIsMedium ? 'Fair availability' : 'Low availability');
                            @endphp
                            <aside class="flex flex-col justify-center rounded-2xl border p-6 text-center {{ $scorePanelClass }}">
                                <p class="text-lg font-semibold {{ $scoreTextClass }}">Overall Availability</p>
                                <p class="mt-6 text-7xl font-semibold leading-none text-slate-950">{{ $score }}</p>
                                <p class="mt-2 text-sm font-medium text-slate-600">out of 100</p>
                                <p class="mx-auto mt-5 rounded-full bg-white/70 px-4 py-2 text-sm font-bold {{ $scoreTextClass }}">{{ $scoreLabel }}</p>
                                <p class="mt-4 text-sm text-slate-600">for {{ ucwords(str_replace('-', ' ', $business_type ?? 'your business')) }}</p>
                            </aside>
                        </div>

                        <div class="mt-6 rounded-2xl border border-cyan-200 bg-cyan-50/70 p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700">GeoAI location assessment</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-950">Why this area and these alternatives?</h2>
                            <p class="mt-3 max-w-4xl text-sm leading-7 text-slate-700">
                                GeoAI assessed {{ $location }} for your {{ ucwords(str_replace('-', ' ', $business_type ?? 'business')) }} using mapped activity inside the {{ $analysisRadius >= 1000 ? ($analysisRadius / 1000) . ' km' : $analysisRadius . ' m' }} range. The location received an availability score of {{ $availability_score }}/100 based on demand, demographics, competition, and accessibility signals.
                            </p>
                            <p class="mt-2 max-w-4xl text-sm leading-7 text-slate-700">
                                Alternative locations are suggested when nearby commercial activity, transport access, or customer visibility indicates a potentially stronger opportunity. Each green marker is accompanied by its analysis score, distance, and the reason it was selected.
                            </p>
                            <form method="POST" action="{{ route('analysis.ask') }}" class="mt-5 border-t border-cyan-200 pt-5">
                                @csrf
                                <input type="hidden" name="analysis_id" value="{{ $record?->id }}">
                                <label for="geoai-question" class="block text-sm font-semibold text-slate-800">Ask GeoAI about this result</label>
                                <div class="mt-2 flex items-center gap-2">
                                    <input id="geoai-question" name="question" type="text" maxlength="500" placeholder="e.g. Why was this location recommended?" value="{{ old('question', $ask_question ?? '') }}" class="min-w-0 flex-1 rounded-full border border-cyan-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-cyan-500" required>
                                    <button type="submit" class="flex shrink-0 items-center gap-2 rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700" aria-label="Enter question" title="Enter question">
                                        <span>Enter</span>
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M12 19V5" stroke-linecap="round" />
                                            <path d="m6 11 6-6 6 6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                            @if ($ask_answer)
                                <div class="mt-4 rounded-xl border border-cyan-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-cyan-700">GeoAI answer</p>
                                    <p class="mt-2 text-sm leading-7 text-slate-700">{{ $ask_answer }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-4 text-sm text-black" data-source-attribution>
                            <span>Population data sourced from</span>
                            <a href="https://open.dosm.gov.my/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-semibold text-black transition hover:text-cyan-700" aria-label="OpenDOSM data portal">
                                <img src="https://open.dosm.gov.my/_next/image?url=%2Fstatic%2Fimages%2Flogo.png&amp;w=96&amp;q=75" alt="OpenDOSM" class="h-8 w-auto" loading="lazy">
                                <span>OpenDOSM</span>
                            </a>
                        </div>

                        @php($dashboard = $geo_ai['dashboard'] ?? [])
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach (($dashboard['headline'] ?? []) as $headline)
                                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                    <p class="text-sm font-semibold text-slate-900">{{ $headline['label'] }}</p>
                                    <p class="mt-5 text-sm leading-6 text-slate-600">{{ $headline['value'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 grid gap-6 lg:grid-cols-2">
                            @foreach ([
                                ['title' => 'Demographics', 'data' => $dashboard['demographics'] ?? [], 'score' => 'score', 'items' => [['Population', 'population', 'number'], ['Median Income', 'median_income', 'currency'], ['Primary Age Group', 'primary_age_group', 'text']]],
                                ['title' => 'Competition', 'data' => $dashboard['competition'] ?? [], 'score' => 'score', 'items' => [['Nearby Competitors', 'nearby_competitors', 'number'], ['Market Saturation', 'market_saturation', 'text']]],
                                ['title' => 'Accessibility', 'data' => $dashboard['accessibility'] ?? [], 'score' => 'score', 'items' => [['Public Transport', 'public_transport', 'text'], ['Parking', 'parking', 'text'], ['Foot Traffic', 'foot_traffic', 'text']]],
                                ['title' => 'Market Demand', 'data' => $dashboard['market_demand'] ?? [], 'score' => 'score', 'items' => [['Current Demand', 'current_demand', 'text'], ['Growth Potential', 'growth_potential', 'text']]],
                            ] as $panel)
                                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                    <div class="flex items-center justify-between gap-4">
                                        <h2 class="text-lg font-semibold text-slate-900">{{ $panel['title'] }}</h2>
                                        <span class="rounded-lg border border-slate-200 px-2 py-1 text-lg font-semibold text-slate-900">{{ (int) ($panel['data'][$panel['score']] ?? 0) }}/100</span>
                                    </div>
                                    <div class="mt-7 space-y-4">
                                        @foreach ($panel['items'] as $item)
                                            <div>
                                                <p class="text-sm font-semibold text-slate-600">{{ $item[0] }}</p>
                                                @if ($item[2] === 'currency')
                                                    <p class="mt-1 text-2xl font-semibold text-slate-950">{{ isset($panel['data'][$item[1]]) ? 'RM ' . number_format((int) $panel['data'][$item[1]]) : 'Unavailable' }}</p>
                                                    @if ($item[1] === 'median_income' && isset($panel['data']['median_income_year']))
                                                        <p class="mt-1 text-xs text-slate-500">{{ $panel['data']['median_income_parlimen'] ?? 'Matched parliamentary constituency' }} · {{ $panel['data']['median_income_year'] }} data</p>
                                                    @endif
                                                @elseif ($item[2] === 'number')
                                                    <p class="mt-1 text-2xl font-semibold text-slate-950">{{ number_format((int) ($panel['data'][$item[1]] ?? 0)) }}</p>
                                                    @if ($item[1] === 'population' && !empty($panel['data']['population_source']))
                                                        <p class="mt-1 text-xs text-slate-500">Source: {{ $panel['data']['population_source'] }}</p>
                                                    @endif
                                                @else
                                                    <p class="mt-1 text-lg text-slate-800">{{ $panel['data'][$item[1]] ?? 'Unavailable' }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h2 class="text-lg font-semibold text-slate-900">Nearby Amenities</h2>
                                <p class="mt-1 text-sm text-slate-500">Key facilities and services in this city</p>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @forelse (($dashboard['amenities'] ?? []) as $amenity)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">{{ $amenity }}</span>
                                    @empty
                                        <span class="text-sm text-slate-500">No mapped amenities found.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Public Transportation Access</h2>
                            <p class="mt-5 text-sm leading-6 text-slate-700">{{ $dashboard['public_transport_access'] ?? 'Transport access data is unavailable.' }}</p>
                        </div>
                        <div class="mt-6 rounded-2xl border-2 border-blue-300 bg-white p-6">
                            <h2 class="text-lg font-semibold text-slate-900">AI Recommendations</h2>
                            <p class="mt-1 text-sm text-slate-500">Strategic insights for your {{ ucwords(str_replace('-', ' ', $business_type ?? 'business')) }}</p>
                            <ul class="mt-5 list-disc space-y-2 pl-5 text-sm leading-6 text-slate-700">
                                @foreach (($dashboard['recommendations'] ?? []) as $recommendation)
                                    <li>{{ $recommendation }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-6 rounded-2xl border-2 border-emerald-300 bg-emerald-50 p-6">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 text-emerald-700">&#9678;</span>
                                <div>
                                    <h2 class="text-lg font-semibold text-emerald-900">Nearby Suggested Locations</h2>
                                    <p class="mt-1 text-sm leading-6 text-emerald-800">These locations are within {{ $analysisRadius >= 1000 ? ($analysisRadius / 1000) . ' km' : $analysisRadius . ' m' }} of {{ $location }} and are shown as green pins on the map above.</p>
                                </div>
                            </div>
                            <div class="mt-5 rounded-xl border border-emerald-200 bg-white/70 p-4 text-sm text-emerald-900">
                                <strong>Tip:</strong> Green pins represent these nearby alternative sites. Each has been evaluated for suitability for your {{ ucwords(str_replace('-', ' ', $business_type ?? 'business')) }}.
                            </div>
                            <div class="mt-4 space-y-3">
                                @foreach (($geo_ai['suggestions'] ?? []) as $index => $suggestion)
                                    <div class="rounded-xl border border-emerald-200 bg-white p-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-emerald-700">#{{ $index + 1 }}</span>
                                            <h3 class="font-semibold text-slate-900">{{ $suggestion['name'] }}</h3>
                                            <span class="rounded-full bg-emerald-600 px-2 py-1 text-xs font-semibold text-white">Score: {{ (int) ($suggestion['score'] ?? 0) }}/100</span>
                                            <span class="text-sm text-slate-500">(~{{ $suggestion['distance_km'] ?? 'N/A' }} km)</span>
                                        </div>
                                        <div class="mt-3 rounded-lg border border-emerald-100 bg-emerald-50 p-3">
                                            <p class="text-xs font-semibold text-emerald-800">Why This Location?</p>
                                            <p class="mt-1 text-sm leading-6 text-emerald-800">{{ $suggestion['reason'] ?? $suggestion['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const searchedLocation = @json($location);
            const selectedCoordinates = @json($geo_ai['coordinates'] ?? [3.139, 101.6869]);
            const suggestedLocations = @json($geo_ai['suggestions'] ?? []);
            const selectedDisplayName = @json($geo_ai['display_name'] ?? $location);
            const selectedWasGeocoded = @json($geo_ai['geocoded'] ?? false);
            const analysisRadius = @json((int) ($geo_ai['radius_meters'] ?? 500));
            const map = L.map('analysis-map').setView(selectedCoordinates, 11);
            const markerBounds = [];

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            const suggestedIcon = L.divIcon({
                className: 'suggested-location-marker',
                html: '<span></span>',
                iconSize: [22, 22],
                iconAnchor: [11, 31],
            });
            const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (character) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;',
            }[character]));

            const addMarker = (coordinates, label, icon) => {
                const marker = L.marker(coordinates, { icon }).addTo(map).bindPopup(label);
                markerBounds.push(coordinates);
                return marker;
            };

            let selectedCircle = L.circle(selectedCoordinates, {
                color: '#dc2626',
                fillColor: '#ef4444',
                fillOpacity: 0.22,
                opacity: 0.85,
                weight: 2,
                radius: analysisRadius,
            }).addTo(map).bindPopup(`<strong>Analysis range</strong><br>${escapeHtml(searchedLocation)}<br><small>${analysisRadius >= 1000 ? `${analysisRadius / 1000} km` : `${analysisRadius} m`}</small>`);
            markerBounds.push(selectedCircle.getBounds());
            suggestedLocations.forEach((suggestion) => addMarker(
                suggestion.coordinates,
                `<strong>${escapeHtml(suggestion.name)}</strong><br>${escapeHtml(suggestion.reason || suggestion.description)}<br><strong>Score: ${escapeHtml(suggestion.score || 'N/A')}/100</strong>`,
                suggestedIcon,
            ));
            map.fitBounds(markerBounds, { padding: [24, 24] });
            window.setTimeout(() => map.invalidateSize(), 150);
        </script>
        <style>
            #analysis-map,
            #analysis-map .leaflet-container { position: relative; z-index: 0; width: 100%; max-width: 100%; overflow: hidden; contain: layout paint; }
            .suggested-location-marker span { position: relative; display: block; transform: rotate(-45deg); border: 3px solid white; border-radius: 50% 50% 50% 0; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.35); }
            .suggested-location-marker span { width: 22px; height: 22px; background: #2eaa4f; }
            .suggested-location-marker span::after { position: absolute; top: 50%; left: 50%; width: 7px; height: 7px; border-radius: 50%; background: white; content: ''; transform: translate(-50%, -50%) rotate(45deg); }
        </style>
    </body>
</html>
