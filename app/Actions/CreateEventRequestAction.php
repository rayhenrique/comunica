<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\CreateEventRequestRequest;
use App\Models\EventRequest;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateEventRequestAction
{
    use AuthorizesRequests;

    public function __construct(private readonly CalculateEventEndAction $calculateEnd)
    {
    }

    /** @param array<string, mixed> $data */
    public function execute(array $data, string $submissionToken): EventRequest
    {
        $this->authorize('create', EventRequest::class);
        if (!Str::isUuid($submissionToken)) {
            throw ValidationException::withMessages(['submission' => 'Atualize o formulário e tente novamente.']);
        }

        $userId = auth()->id();
        $key = 'request-submission:'.hash('sha256', $userId.'|'.$submissionToken);

        return Cache::lock($key.':lock', 60)->block(5, function () use ($data, $userId, $key): EventRequest {
            $existingId = Cache::get($key);
            if ($existingId !== null) {
                return EventRequest::withTrashed()->where('user_id', $userId)->findOrFail($existingId);
            }

            $definition = new CreateEventRequestRequest();
            $validated = Validator::make($data, $definition->rules(), attributes: $definition->attributes())->validate();
            $start = CarbonImmutable::parse($validated['start_at'], config('app.timezone'))->setTimezone(config('app.timezone'));

            $event = DB::transaction(function () use ($validated, $userId, $start): EventRequest {
                $event = new EventRequest($validated);
                $event->user_id = $userId;
                $event->start_at = $start;
                $event->end_at = $this->calculateEnd->execute($start, (int) $validated['duration_minutes']);
                $event->status = 'pending';
                $event->save();

                return $event;
            });
            Cache::put($key, $event->id, now()->addMinutes(30));

            return $event;
        });
    }
}
