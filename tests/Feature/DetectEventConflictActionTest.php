<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\DetectEventConflictAction;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectEventConflictActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_conflicts_require_overlap_and_same_location_or_two_coverage_demands(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $candidate = $this->event(['status' => 'pending']);
        $sameLocationArt = $this->event(['demand_type' => 'art']);
        $otherLocationCoverage = $this->event(['location' => 'Outro local', 'status' => 'rescheduled']);
        $contained = $this->event(['start_at' => '2027-01-10 09:15:00', 'end_at' => '2027-01-10 09:45:00']);
        $containing = $this->event(['start_at' => '2027-01-10 08:00:00', 'end_at' => '2027-01-10 11:00:00']);
        $this->event(['location' => 'Outro local', 'demand_type' => 'art']);
        $this->event(['start_at' => '2027-01-10 08:00:00', 'end_at' => '2027-01-10 09:00:00']);
        $this->event(['start_at' => '2027-01-10 10:00:00', 'end_at' => '2027-01-10 11:00:00']);
        foreach (['pending', 'rejected', 'cancelled'] as $status) {
            $this->event(['status' => $status]);
        }
        $this->event()->delete();

        $conflicts = app(DetectEventConflictAction::class)->execute($candidate);
        $this->assertEqualsCanonicalizing([
            $sameLocationArt->id, $otherLocationCoverage->id, $contained->id, $containing->id,
        ], $conflicts->modelKeys());
        $this->assertSame('pending', $candidate->fresh()->status);
    }

    public function test_art_only_has_location_conflicts_and_end_is_calculated_from_duration(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $candidate = $this->event(['status' => 'pending', 'demand_type' => 'art', 'end_at' => '2027-01-10 23:00:00']);
        $sameLocation = $this->event(['demand_type' => 'both']);
        $this->event(['location' => 'Outro local']);
        $this->event(['start_at' => '2027-01-10 12:00:00', 'end_at' => '2027-01-10 13:00:00']);

        $this->assertSame([$sameLocation->id], app(DetectEventConflictAction::class)->execute($candidate)->modelKeys());
    }

    public function test_confirmed_candidate_does_not_conflict_with_itself(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $candidate = $this->event();
        $this->assertCount(0, app(DetectEventConflictAction::class)->execute($candidate));
    }

    public function test_coordinators_cannot_use_conflict_detection_to_read_other_requests(): void
    {
        $candidate = $this->event();
        $this->actingAs($candidate->user);
        $this->expectException(AuthorizationException::class);
        app(DetectEventConflictAction::class)->execute($candidate);
    }

    /** @param array<string, mixed> $overrides */
    private function event(array $overrides = []): EventRequest
    {
        return EventRequest::forceCreate([
            'user_id' => User::factory()->create()->id,
            'title' => 'Pauta de teste', 'demand_type' => 'coverage', 'location' => 'Auditório',
            'target_audience' => 'Comunidade', 'status' => 'approved',
            'start_at' => '2027-01-10 09:00:00', 'end_at' => '2027-01-10 10:00:00',
            'duration_minutes' => 60,
            ...$overrides,
        ]);
    }
}
