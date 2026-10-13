<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use Illuminate\Database\Eloquent\Collection;

class GetPublicCalendarEventsAction
{
    /** @return Collection<int, EventRequest> */
    public function execute(int $year, int $month): Collection
    {
        return EventRequest::confirmed()->inMonth($year, $month)
            ->select(['id', 'title', 'demand_type', 'start_at', 'end_at', 'location', 'target_audience'])
            ->orderBy('start_at')->orderBy('id')->get();
    }
}
