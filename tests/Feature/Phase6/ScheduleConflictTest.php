<?php

declare(strict_types=1);

use App\Actions\DetectEventConflictAction;
use App\Actions\ModerateEventAction;
use App\Models\User;
use Tests\Support\Phase6Fixtures;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
});

it('detects overlapping confirmed events at the same location', function (string $start, string $end): void {
    $owner = User::factory()->create();
    $candidate = Phase6Fixtures::event($owner);
    $confirmed = Phase6Fixtures::event($owner, [
        'status' => 'approved', 'start_at' => $start, 'end_at' => $end,
    ]);
    expect(app(DetectEventConflictAction::class)->execute($candidate)->modelKeys())->toBe([$confirmed->id]);
})->with([
    'identical' => ['2027-01-10 09:00:00', '2027-01-10 10:00:00'],
    'partial before' => ['2027-01-10 08:30:00', '2027-01-10 09:30:00'],
    'partial after' => ['2027-01-10 09:30:00', '2027-01-10 10:30:00'],
    'contained' => ['2027-01-10 09:15:00', '2027-01-10 09:45:00'],
    'containing' => ['2027-01-10 08:00:00', '2027-01-10 11:00:00'],
]);

it('does not warn for adjacent intervals unconfirmed deleted or other-location art', function (): void {
    $owner = User::factory()->create();
    $candidate = Phase6Fixtures::event($owner);
    Phase6Fixtures::event($owner, ['status' => 'approved', 'start_at' => '2027-01-10 08:00:00', 'end_at' => '2027-01-10 09:00:00']);
    Phase6Fixtures::event($owner, ['status' => 'approved', 'start_at' => '2027-01-10 10:00:00', 'end_at' => '2027-01-10 11:00:00']);
    Phase6Fixtures::event($owner, ['status' => 'approved', 'location' => 'Outro auditório']);
    Phase6Fixtures::event($owner, ['status' => 'approved'])->delete();
    foreach (['pending', 'cancelled', 'rejected'] as $status) {
        Phase6Fixtures::event($owner, ['status' => $status]);
    }
    expect(app(DetectEventConflictAction::class)->execute($candidate))->toHaveCount(0);
});

it('warns for simultaneous coverage across locations and still permits approval', function (string $candidateType, string $confirmedType): void {
    $owner = User::factory()->create();
    $candidate = Phase6Fixtures::event($owner, ['demand_type' => $candidateType]);
    $confirmed = Phase6Fixtures::event($owner, [
        'status' => 'rescheduled', 'demand_type' => $confirmedType, 'location' => 'Outro auditório',
    ]);
    expect(app(DetectEventConflictAction::class)->execute($candidate)->modelKeys())->toBe([$confirmed->id]);
    app(ModerateEventAction::class)->execute($candidate, 'approve');
    expect($candidate->fresh()->status)->toBe('approved');
    expect($confirmed->fresh()->status)->toBe('rescheduled');
})->with(['coverage', 'both'])->with(['coverage', 'both']);
