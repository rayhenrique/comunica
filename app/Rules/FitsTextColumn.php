<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FitsTextColumn implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && strlen($value) > 65535) {
            $fail('Este texto é longo demais. Reduza o conteúdo antes de continuar.');
        }
    }
}
