<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class CalculateEventEndAction
{
    public function execute(DateTimeInterface $startAt, int $durationMinutes): CarbonImmutable
    {
        if ($durationMinutes < 1 || $durationMinutes > 65535) {
            throw ValidationException::withMessages([
                'duration_minutes' => 'A duração deve ser um inteiro entre 1 e 65535 minutos.',
            ]);
        }

        return CarbonImmutable::instance($startAt)->addMinutes($durationMinutes);
    }
}
