<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Support\EventLabels;

class ListVisibleEventRequestsAction
{
    /** @return LengthAwarePaginator<int, EventRequest> */
    public function execute(User $user, string $status = ''): LengthAwarePaginator
    {
        Gate::forUser($user)->authorize('viewAny', EventRequest::class);
        Validator::make(compact('status'), ['status' => ['nullable', Rule::in(array_keys(EventLabels::statuses()))]])->validate();

        $query = $user->isCommunicationAdmin()
            ? EventRequest::query()
            : $user->eventRequests()->getQuery();

        return $query->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(15);
    }
}
