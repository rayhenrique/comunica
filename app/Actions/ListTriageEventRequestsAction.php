<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EventRequest;
use App\Support\EventLabels;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ListTriageEventRequestsAction
{
    use AuthorizesRequests;

    /**
     * @param array<string, string> $filters
     * @return LengthAwarePaginator<int, EventRequest>
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        $this->authorize('moderate', EventRequest::class);
        Validator::make($filters, [
            'status' => ['nullable', Rule::in(array_keys(EventLabels::statuses()))],
            'department' => ['nullable', 'string', 'max:100'],
            'demand_type' => ['nullable', Rule::in(array_keys(EventLabels::demands()))],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...(($filters['from'] ?? '') !== '' ? ['after_or_equal:from'] : [])],
        ], attributes: ['from' => 'data inicial', 'to' => 'data final', 'department' => 'setor',
            'demand_type' => 'demanda', 'status' => 'status'])->validate();

        return EventRequest::with('user')
            ->when(($filters['status'] ?? '') !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when(($filters['department'] ?? '') !== '', fn ($query) => $query->whereHas('user', fn ($users) => $users->where('department', $filters['department'])))
            ->when(($filters['demand_type'] ?? '') !== '', fn ($query) => $query->where('demand_type', $filters['demand_type']))
            ->when(($filters['from'] ?? '') !== '', fn ($query) => $query->where('start_at', '>=', CarbonImmutable::parse($filters['from'])->startOfDay()->toDateTimeString()))
            ->when(($filters['to'] ?? '') !== '', fn ($query) => $query->where('start_at', '<', CarbonImmutable::parse($filters['to'])->addDay()->startOfDay()->toDateTimeString()))
            ->orderBy('start_at')->orderBy('id')->paginate(15);
    }
}
