<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_analysis_requires_authentication(): void
    {
        $response = $this->get('/analysis/business-type');

        $response->assertRedirect('/login');
    }

    public function test_guest_start_analysis_link_goes_to_login(): void
    {
        $this->get('/')->assertSee('href="http://localhost/login"', false);
    }

    public function test_authenticated_start_analysis_link_skips_login(): void
    {
        $user = User::factory()->create(['name' => 'Alice Tan']);

        $this->actingAs($user)
            ->get('/')
            ->assertSee('href="http://localhost/analysis/business-type"', false)
            ->assertSee('Hi, Alice Tan');
    }

    public function test_authenticated_home_shows_saved_analysis_history_and_account_actions(): void
    {
        $user = User::factory()->create();
        $user->analysisRecords()->create([
            'business_type' => 'fitness',
            'location' => 'Kajang',
            'status' => 'completed',
            'meta' => ['availability_score' => 72],
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertSee('id="open-account-menu"', false)
            ->assertSee('Hi, ' . $user->name)
            ->assertSee('Previous analysis')
            ->assertSee('action="http://localhost/logout"', false);
    }

    public function test_authenticated_history_page_lists_only_the_users_records(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $record = $user->analysisRecords()->create([
            'business_type' => 'fitness',
            'location' => 'Kajang',
            'status' => 'completed',
        ]);
        $otherUser->analysisRecords()->create([
            'business_type' => 'bakery',
            'location' => 'Ipoh',
            'status' => 'completed',
        ]);

        $this->actingAs($user)
            ->get('/analysis/history')
            ->assertOk()
            ->assertSee('Kajang')
            ->assertSee(route('analysis.result', ['id' => $record->id]), false)
            ->assertDontSee('Ipoh');
    }

    public function test_authenticated_history_page_shows_empty_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/analysis/history')
            ->assertOk()
            ->assertSee('No previous analyses yet.');
    }

    public function test_authenticated_user_can_store_analysis_record(): void
    {
        $user = User::factory()->create([
            'name' => 'Alice Tan',
            'email' => 'alice@example.com',
            'password' => bcrypt('password123'),
            'phone' => '+60123456789',
        ]);

        $this->actingAs($user)
            ->post('/analysis/store', [
                'business_type' => 'food-beverage',
                'location' => 'Petaling Jaya, Selangor',
                'radius' => '1500',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('analysis_records', [
            'user_id' => $user->id,
            'business_type' => 'food-beverage',
            'location' => 'Petaling Jaya, Selangor',
            'status' => 'completed',
        ]);

        $record = $user->analysisRecords()->latest()->first();
        $this->assertIsArray($record->meta['geoai']);
        $this->assertSame(1500, $record->meta['geoai']['radius_meters']);
        $this->assertCount(2, $record->meta['geoai']['coordinates']);
        $this->assertNotEmpty($record->meta['geoai']['suggestions']);
        $this->assertArrayHasKey('demographics', $record->meta['geoai']['dashboard']);
        $this->assertArrayHasKey('competition', $record->meta['geoai']['dashboard']);
        $this->assertArrayHasKey('accessibility', $record->meta['geoai']['dashboard']);
        $this->assertArrayHasKey('market_demand', $record->meta['geoai']['dashboard']);
        $this->assertArrayHasKey('distance_km', $record->meta['geoai']['suggestions'][0]);
        $this->assertArrayHasKey('score', $record->meta['geoai']['suggestions'][0]);
        $this->assertArrayHasKey('reason', $record->meta['geoai']['suggestions'][0]);
        foreach ($record->meta['geoai']['suggestions'] as $suggestion) {
            $this->assertLessThanOrEqual(1.5, $suggestion['distance_km']);
        }
        $this->assertNotSame(48, $record->meta['geoai']['score']);
        $reasons = array_column($record->meta['geoai']['suggestions'], 'reason');
        $this->assertCount(count($reasons), array_unique($reasons));
    }

    public function test_analysis_result_keeps_map_circle_centered_on_analysis_coordinates(): void
    {
        $this->assertStringNotContainsString(
            'nominatim.openstreetmap.org/search?format=jsonv2',
            file_get_contents(resource_path('views/analysis/result.blade.php')),
        );
    }

    public function test_analysis_fallback_preserves_coordinates_after_geocoding(): void
    {
        $script = file_get_contents(base_path('scripts/geoai_analysis.py'));

        $this->assertStringContainsString(
            'coordinates = [latitude, longitude] if latitude is not None and longitude is not None else None',
            $script,
        );
        $this->assertStringContainsString(
            'return fallback(location, business_type, radius_meters, coordinates)',
            $script,
        );
    }

    public function test_authenticated_user_can_ask_geoai_about_an_analysis(): void
    {
        $user = User::factory()->create();
        $record = $user->analysisRecords()->create([
            'business_type' => 'fitness',
            'location' => 'Kajang',
            'status' => 'completed',
            'meta' => [
                'availability_score' => 72,
                'geoai' => [
                    'radius_meters' => 1500,
                    'summary' => 'The fitness opportunity scores 72/100.',
                    'dashboard' => [
                        'competition' => ['nearby_competitors' => 5, 'market_saturation' => 'Moderate'],
                    ],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->post(route('analysis.ask'), [
                'analysis_id' => $record->id,
                'question' => 'How many competitors are nearby?',
            ])
            ->assertRedirect(route('analysis.result', ['id' => $record->id]))
            ->assertSessionMissing('ask_question')
            ->assertSessionHas('ask_answer', fn (string $answer): bool => str_contains($answer, '5 nearby competitors'));
    }
}
