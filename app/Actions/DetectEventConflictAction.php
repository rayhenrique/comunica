<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

class DetectEventConflictAction
{
    use AuthorizesRequests;

    public function __construct(private readonly CalculateEventEndAction $calculateEnd)
    {
    }

    /** @return Collection<int, EventRequest> */
    public function execute(EventRequest $eventRequest): Collection
    {
        $this->authorize('moderate', EventRequest::class);

        if ($eventRequest->start_at === null) {
            throw ValidationException::withMessages(['start_at' => 'Informe uma data e hora de início válida.']);
        }

        $end = $this->calculateEnd->execute($eventRequest->start_at, $eventRequest->duration_minutes);
        $needsCoverage = in_array($eventRequest->demand_type, ['coverage', 'both'], true);

        return EventRequest::confirmed()
            ->when($eventRequest->getKey() !== null, fn (Builder $query): Builder => $query->whereKeyNot($eventRequest->getKey()))
            ->where('start_at', '<', $end->toDateTimeString())
            ->where('end_at', '>', $eventRequest->start_at->toDateTimeString())
            ->where(function (Builder $query) use ($eventRequest, $needsCoverage): void {
                $query->where('location', $eventRequest->location);

                if ($needsCoverage) {
                    $query->orWhereIn('demand_type', ['coverage', 'both']);
                }
            })
            ->orderBy('start_at')->orderBy('id')->get();
    }
}
