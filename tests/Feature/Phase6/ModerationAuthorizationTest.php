<?php

declare(strict_types=1);

use App\Actions\ModerateEventAction;
use App\Livewire\Communication\Triage;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Livewire;
use Tests\Support\Phase6Fixtures;

it('denies every moderation decision to the owner and another coordinator without changing data', function (string $decision, bool $isOwner): void {
    $owner = User::factory()->create();
    $event = Phase6Fixtures::event($owner);
    $before = $event->refresh()->getRawOriginal();
    $this->actingAs($isOwner ? $owner : User::factory()->create());

    expect(fn () => app(ModerateEventAction::class)->execute(
        $event,
        $decision,
        '2027-01-11T14:30:00',
        'Justificativa válida de teste.',
    ))->toThrow(AuthorizationException::class);

    expect($event->fresh()->getRawOriginal())->toBe($before);
    $this->get('/comunicacao/triagem')->assertForbidden();
    Livewire::test(Triage::class)->assertForbidden();
})->with(['approve', 'reschedule', 'reject'])->with([true, false]);

it('denies unauthenticated action calls without changing data', function (): void {
    $event = Phase6Fixtures::event(User::factory()->create());
    $before = $event->refresh()->getRawOriginal();
    expect(fn () => app(ModerateEventAction::class)->execute($event, 'approve'))
        ->toThrow(AuthorizationException::class);
    expect($event->fresh()->getRawOriginal())->toBe($before);
});

it('allows communication to reschedule with preserved original date and audit', function (): void {
    $admin = User::factory()->create(['role' => 'communication_admin']);
    $event = Phase6Fixtures::event(User::factory()->create());
    $this->actingAs($admin);
    app(ModerateEventAction::class)->execute($event, 'reschedule', '2027-01-11T14:30:00', 'Adequação da agenda.');
    $saved = $event->fresh();
    expect($saved->status)->toBe('rescheduled');
    expect($saved->original_start_at->toDateTimeString())->toBe('2027-01-10 09:00:00');
    expect($saved->start_at->toDateTimeString())->toBe('2027-01-11 14:30:00');
    expect($saved->end_at->toDateTimeString())->toBe('2027-01-11 15:30:00');
    expect($saved->moderated_by)->toBe($admin->id);
    expect($saved->moderated_at)->not->toBeNull();
});
