<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Services\GeoAiAnalyzer;

Route::get('/', function () {
    return view('welcome', [
        'analysisRecords' => Auth::check()
            ? Auth::user()->analysisRecords()->latest()->get()
            : collect(),
    ]);
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'login_method' => ['required', 'in:email,phone'],
        'email' => ['nullable', 'required_if:login_method,email', 'email'],
        'phone' => ['nullable', 'required_if:login_method,phone', 'string', 'max:20'],
        'password' => ['required', 'string'],
    ]);

    $credentials = $request->login_method === 'email'
        ? ['email' => $request->email, 'password' => $request->password]
        : ['phone' => $request->phone, 'password' => $request->password];

    if (! Auth::attempt($credentials, true)) {
        return back()->withErrors(['login' => 'Invalid email or phone number and password. Please try again.'])->withInput();
    }

    $request->session()->regenerate();

    return redirect()->intended(route('analysis.business'));
})->name('login.submit');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'register_method' => ['required', 'in:email,phone'],
        'email' => ['nullable', 'required_if:register_method,email', 'email', 'unique:users,email'],
        'phone' => ['nullable', 'required_if:register_method,phone', 'string', 'max:20', 'unique:users,phone'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $name = $request->name;
    $method = $request->register_method;

    if ($method === 'email') {
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    } else {
        $user = User::create([
            'name' => $name,
            'email' => 'geoai-' . Str::uuid() . '@example.com',
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
    }

    Auth::login($user, true);

    return redirect()->route('analysis.business');
})->name('register.submit');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');

    Route::get('/analysis/history', function () {
        return view('analysis.history', [
            'analysisRecords' => auth()->user()->analysisRecords()->latest()->get(),
        ]);
    })->name('analysis.history');

    Route::get('/analysis/business-type', function () {
        return view('analysis.business-type');
    })->name('analysis.business');

    Route::get('/analysis/location', function () {
        return view('analysis.location-selection');
    })->name('analysis.location');

    Route::post('/analysis/store', function (Illuminate\Http\Request $request) {
        $request->validate([
            'business_type' => ['required', 'string'],
            'location' => ['required', 'string'],
            'radius' => ['nullable', 'integer', 'in:500,1500,3000'],
            'features' => ['nullable', 'array'], // 接受可选特征
        ]);

        $geoAiAnalysis = app(App\Services\GeoAiAnalyzer::class)->analyze(
            $request->input('location'),
            $request->input('business_type'),
            array_merge($request->input('features', []), [
                'radius_meters' => (int) $request->input('radius', 500),
            ])
        );

        $record = auth()->user()->analysisRecords()->create([
            'business_type' => $request->input('business_type'),
            'location' => $request->input('location'),
            'status' => 'completed',
            'meta' => [
                'source' => 'web',
                'submitted_at' => now()->toDateTimeString(),
                'availability_score' => $geoAiAnalysis['score'],
                'geoai' => $geoAiAnalysis,
            ],
        ]);

        return redirect()->route('analysis.result', [
            'id' => $record->id, 
            'business_type' => $record->business_type, 
            'location' => $record->location
        ]);
    })->name('analysis.store');

    Route::post('/analysis/ask', function (Request $request) {
        $request->validate([
            'analysis_id' => ['required', 'integer'],
            'question' => ['required', 'string', 'max:500'],
        ]);

        $record = auth()->user()->analysisRecords()->findOrFail($request->integer('analysis_id'));
        $analysis = $record->meta['geoai'] ?? [];
        $dashboard = $analysis['dashboard'] ?? [];
        $question = Str::lower($request->string('question')->toString());
        $radius = (int) ($analysis['radius_meters'] ?? 500);
        $radiusLabel = $radius >= 1000 ? ($radius / 1000) . ' km' : $radius . ' m';
        $suggestion = $analysis['suggestions'][0] ?? null;

        if (Str::contains($question, ['competitor', 'competition', 'saturation'])) {
            $competition = $dashboard['competition'] ?? [];
            $answer = sprintf(
                'The analysis found %s nearby competitors with %s market saturation. This is based on mapped commercial activity within the selected %s range.',
                $competition['nearby_competitors'] ?? 'limited',
                strtolower($competition['market_saturation'] ?? 'unavailable'),
                $radiusLabel
            );
        } elseif (Str::contains($question, ['transport', 'access', 'traffic', 'parking'])) {
            $accessibility = $dashboard['accessibility'] ?? [];
            $answer = sprintf(
                'Accessibility is rated %s/100. Public transport is %s, while parking is noted as: %s.',
                $accessibility['score'] ?? 'unavailable',
                strtolower($accessibility['public_transport'] ?? 'unavailable'),
                strtolower($accessibility['parking'] ?? 'unavailable')
            );
        } elseif (Str::contains($question, ['population', 'demographic', 'people', 'customer'])) {
            $demographics = $dashboard['demographics'] ?? [];
            $answer = sprintf(
                'The selected area has an estimated population of %s, with a primary age profile of %s. Use this as an initial market signal and validate it with local research.',
                isset($demographics['population']) ? number_format((int) $demographics['population']) : 'unavailable',
                strtolower($demographics['primary_age_group'] ?? 'mixed age groups')
            );
        } elseif (Str::contains($question, ['suggest', 'recommend', 'alternative', 'other', 'why'])) {
            $answer = $suggestion
                ? sprintf('The strongest alternative is %s, about %s km away. It was suggested because %s', $suggestion['name'], $suggestion['distance_km'] ?? 'an unknown distance', strtolower($suggestion['reason'] ?? $suggestion['description'] ?? 'it shows promising nearby activity.'))
                : 'No alternative location was available in the current analysis.';
        } elseif (Str::contains($question, ['range', 'radius', 'far', 'area'])) {
            $answer = "This analysis covers a {$radiusLabel} radius around {$record->location}. The map's red circle shows that same area.";
        } else {
            $answer = ($analysis['summary'] ?? 'The analysis is based on the location data and mapped activity available for this search.') . ' Ask about competition, transport, population, recommendations, or the analysis range for a more specific answer.';
        }

        return redirect()->route('analysis.result', ['id' => $record->id])
            ->with('ask_answer', $answer);
    })->name('analysis.ask');

    Route::get('/analysis/result', function (Request $request) {
        $record = auth()->user()->analysisRecords()->find($request->integer('id'))
            ?? auth()->user()->analysisRecords()->latest()->first();
        $availabilityScore = (int) ($record?->meta['availability_score'] ?? 72);
        $geoAiAnalysis = $record?->meta['geoai'] ?? [];

        return view('analysis.result', [
            'record' => $record,
            'location' => $request->input('location', $record?->location ?? 'Not provided yet'),
            'business_type' => $request->input('business_type', $record?->business_type ?? 'Not provided yet'),
            'availability_score' => $availabilityScore,
            'geo_ai' => $geoAiAnalysis,
            'ask_question' => session('ask_question'),
            'ask_answer' => session('ask_answer'),
        ]);
    })->name('analysis.result');
});
