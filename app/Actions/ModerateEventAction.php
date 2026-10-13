<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use App\Rules\FitsTextColumn;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ModerateEventAction
{
    use AuthorizesRequests;

    public function __construct(private readonly CalculateEventEndAction $calculateEnd)
    {
    }

    public function execute(
        EventRequest $eventRequest,
        string $decision,
        ?string $newStartAt = null,
        ?string $notes = null,
    ): EventRequest {
        $this->authorize('moderate', EventRequest::class);
        $notes = $notes === null ? null : trim($notes);

        Validator::make([
            'decision' => $decision, 'start_at' => $newStartAt, 'moderation_notes' => $notes,
        ], [
            'decision' => ['required', Rule::in(['approve', 'reschedule', 'reject'])],
            'start_at' => $decision === 'reschedule' ? ['required', 'date'] : ['nullable'],
            'moderation_notes' => match ($decision) {
                'reject' => ['required', 'string', 'min:10', new FitsTextColumn()],
                'reschedule' => ['required', 'string', 'min:1', new FitsTextColumn()],
                default => ['nullable'],
            },
        ], [
            'moderation_notes.required' => 'Informe a justificativa da decisão.',
            'moderation_notes.min' => 'A justificativa deve ter no mínimo :min caracteres.',
            'start_at.required' => 'Informe a nova data e hora de início.',
            'start_at.date' => 'Informe uma nova data e hora válida.',
            'decision.in' => 'A decisão deve ser aprovar, reagendar ou recusar.',
        ])->validate();

        return DB::transaction(function () use ($eventRequest, $decision, $newStartAt, $notes): EventRequest {
            $locked = EventRequest::query()->lockForUpdate()->findOrFail($eventRequest->getKey());
            $this->authorize('moderate', $locked);

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Somente solicitações pendentes podem ser moderadas.']);
            }

            if ($decision === 'reschedule') {
                $locked->original_start_at ??= $locked->start_at;
                $locked->start_at = CarbonImmutable::parse($newStartAt, config('app.timezone'))
                    ->setTimezone(config('app.timezone'));
            }

            if ($decision !== 'reject') {
                $locked->end_at = $this->calculateEnd->execute($locked->start_at, $locked->duration_minutes);
            }

            $locked->status = match ($decision) {
                'approve' => 'approved',
                'reschedule' => 'rescheduled',
                'reject' => 'rejected',
            };
            $locked->moderation_notes = $decision === 'approve' ? null : $notes;
            $locked->moderated_by = auth()->id();
            $locked->moderated_at = now();
            $locked->save();

            return $locked;
        }, attempts: 3);
    }
}
