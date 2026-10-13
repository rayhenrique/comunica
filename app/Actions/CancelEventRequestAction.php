<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class CancelEventRequestAction
{
    use AuthorizesRequests;

    public function execute(int $eventRequestId): EventRequest
    {
        return DB::transaction(function () use ($eventRequestId): EventRequest {
            $event = EventRequest::query()->lockForUpdate()->findOrFail($eventRequestId);
            $this->authorize('cancel', $event);
            $event->status = 'cancelled';
            $event->save();

            return $event;
        }, attempts: 3);
    }
}
