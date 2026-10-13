<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EventRequest;
use App\Models\User;

class EventRequestPolicy
{
    public function viewAny(User $user): bool
    {
        // A consulta também deve restringir coordenadores às próprias solicitações.
        return $user->isCommunicationAdmin() || $user->isCoordinator();
    }

    public function view(User $user, EventRequest $eventRequest): bool
    {
        return !$eventRequest->trashed()
            && ($user->isCommunicationAdmin() || $this->isOwner($user, $eventRequest));
    }

    public function create(User $user): bool
    {
        return $user->isCoordinator();
    }

    public function moderate(User $user): bool
    {
        return $user->isCommunicationAdmin();
    }

    public function cancel(User $user, EventRequest $eventRequest): bool
    {
        return !$eventRequest->trashed()
            && $eventRequest->status === 'pending'
            && $this->isOwner($user, $eventRequest);
    }

    private function isOwner(User $user, EventRequest $eventRequest): bool
    {
        return $user->isCoordinator()
            && $user->getKey() !== null
            && (int) $user->getKey() === (int) $eventRequest->user_id;
    }
}
