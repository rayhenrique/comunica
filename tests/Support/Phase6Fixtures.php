<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\EventRequest;
use App\Models\User;

final class Phase6Fixtures
{
    /** @param array<string, mixed> $overrides */
    public static function event(User $owner, array $overrides = []): EventRequest
    {
        return EventRequest::forceCreate([
            'user_id' => $owner->id,
            'title' => 'Pauta de aceite',
            'demand_type' => 'art',
            'location' => 'Auditório central',
            'target_audience' => 'Comunidade',
            'description' => 'Briefing privado de aceite',
            'status' => 'pending',
            'start_at' => '2027-01-10 09:00:00',
            'end_at' => '2027-01-10 10:00:00',
            'duration_minutes' => 60,
            ...$overrides,
        ]);
    }

    /** @return array<string, mixed> */
    public static function submission(string $start): array
    {
        return [
            'title' => 'Pauta de aceite',
            'demand_type' => 'art',
            'location' => 'Auditório central',
            'target_audience' => 'Comunidade',
            'description' => 'Briefing de aceite',
            'start_at' => $start,
            'duration_minutes' => 60,
        ];
    }
}
