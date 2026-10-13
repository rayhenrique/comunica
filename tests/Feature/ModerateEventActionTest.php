<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ModerateEventAction;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class ModerateEventActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_calculates_end_and_audits_current_user_even_with_conflicts(): void
    {
        $admin = User::factory()->create(['role' => 'communication_admin']);
        $this->actingAs($admin);
        $event = $this->event();
        $this->event(['status' => 'approved']);
        $result = app(ModerateEventAction::class)->execute($event, 'approve');

        $this->assertSame('approved', $result->fresh()->status);
        $this->assertSame('2027-01-10 10:00:00', $result->end_at->toDateTimeString());
        $this->assertSame('2027-01-10 09:00:00', $result->start_at->toDateTimeString());
        $this->assertSame($admin->id, $result->moderated_by);
        $this->assertNotNull($result->moderated_at);
        $this->assertNull($result->original_start_at);
    }

    public function test_rescheduling_preserves_original_start_and_recalculates_end(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        $result = app(ModerateEventAction::class)->execute(
            $event,
            'reschedule',
            '2027-01-11T14:30',
            '  Adequação da agenda institucional.  ',
        );

        $this->assertSame('rescheduled', $result->status);
        $this->assertSame('2027-01-10 09:00:00', $result->original_start_at->toDateTimeString());
        $this->assertSame('2027-01-11 14:30:00', $result->start_at->toDateTimeString());
        $this->assertSame('2027-01-11 15:30:00', $result->end_at->toDateTimeString());
        $this->assertSame('Adequação da agenda institucional.', $result->moderation_notes);
    }

    public function test_rejection_requires_a_reason_and_preserves_dates(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        $result = app(ModerateEventAction::class)->execute($event, 'reject', notes: 'Equipe indisponível nesta data.');
        $this->assertSame('rejected', $result->status);
        $this->assertSame($event->getRawOriginal('start_at'), $result->getRawOriginal('start_at'));
        $this->assertSame($event->getRawOriginal('end_at'), $result->getRawOriginal('end_at'));
    }

    public function test_invalid_decisions_and_missing_or_short_reasons_leave_request_unchanged(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        foreach ([['reject', null, 'curta'], ['reschedule', null, 'Motivo'],
            ['reschedule', 'data inválida', 'Motivo'], ['reschedule', '2027-01-11 09:00:00', '   '],
            ['unknown', null, null]] as [$decision, $start, $notes]) {
            try {
                app(ModerateEventAction::class)->execute($event, $decision, $start, $notes);
                $this->fail('A decisão inválida deveria ser rejeitada.');
            } catch (ValidationException) {
                $this->assertSame('pending', $event->fresh()->status);
                $this->assertNull($event->fresh()->moderated_by);
            }
        }
    }

    public function test_coordinators_and_guests_cannot_moderate(): void
    {
        $event = $this->event();
        foreach ([null, $event->user] as $user) {
            auth()->forgetGuards();
            if ($user !== null) {
                $this->actingAs($user);
            }
            try {
                app(ModerateEventAction::class)->execute($event, 'approve');
                $this->fail('A moderação deveria ser negada.');
            } catch (AuthorizationException) {
                $this->assertSame('pending', $event->fresh()->status);
            }
        }
    }

    public function test_stale_model_cannot_overwrite_an_existing_decision(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        $stale = EventRequest::findOrFail($event->id);
        app(ModerateEventAction::class)->execute($event, 'approve');
        try {
            app(ModerateEventAction::class)->execute($stale, 'reject', notes: 'Nova decisão indevida.');
            $this->fail('A decisão já gravada deveria ser preservada.');
        } catch (ValidationException) {
            $this->assertSame('approved', $stale->fresh()->status);
        }
    }

    public function test_cancelled_requests_cannot_be_moderated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event(['status' => 'cancelled']);
        $this->expectException(ValidationException::class);
        app(ModerateEventAction::class)->execute($event, 'approve');
    }

    public function test_deleted_requests_cannot_be_moderated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        $event->delete();
        $this->expectException(ModelNotFoundException::class);
        app(ModerateEventAction::class)->execute($event, 'approve');
    }

    public function test_transaction_rolls_back_when_persistence_fails(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']));
        $event = $this->event();
        $dispatcher = EventRequest::getEventDispatcher();
        EventRequest::setEventDispatcher(clone $dispatcher);
        EventRequest::saving(function () use ($event): void {
            DB::table('event_requests')->where('id', $event->id)->update(['title' => 'Alteração a reverter']);
            throw new RuntimeException('Falha de persistência simulada');
        });

        try {
            app(ModerateEventAction::class)->execute($event, 'approve');
            $this->fail('A falha deveria ser propagada.');
        } catch (RuntimeException) {
            $this->assertSame('Pauta de teste', $event->fresh()->title);
            $this->assertSame('pending', $event->fresh()->status);
            $this->assertNull($event->fresh()->moderated_by);
        } finally {
            EventRequest::setEventDispatcher($dispatcher);
        }
    }

    /** @param array<string, mixed> $overrides */
    private function event(array $overrides = []): EventRequest
    {
        return EventRequest::forceCreate([
            'user_id' => User::factory()->create()->id,
            'title' => 'Pauta de teste', 'demand_type' => 'coverage', 'location' => 'Auditório',
            'target_audience' => 'Comunidade', 'status' => 'pending',
            'start_at' => '2027-01-10 09:00:00', 'end_at' => '2027-01-10 10:00:00',
            'duration_minutes' => 60,
            ...$overrides,
        ]);
    }
}
