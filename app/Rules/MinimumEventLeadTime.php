<?php

declare(strict_types=1);

namespace App\Rules;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

class MinimumEventLeadTime implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Informe uma data e hora de início válida.');
            return;
        }

        try {
            $start = CarbonImmutable::parse($value, config('app.timezone'));
        } catch (Throwable) {
            $fail('Informe uma data e hora de início válida.');
            return;
        }

        if ($start->lessThan(CarbonImmutable::now(config('app.timezone'))->addHours(48))) {
            $fail('A solicitação deve ter antecedência mínima de 48 horas corridas.');
        }
    }
}
