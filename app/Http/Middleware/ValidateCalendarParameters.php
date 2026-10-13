<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCalendarParameters
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['year' => [1000, 9998], 'month' => [1, 12]] as $name => [$minimum, $maximum]) {
            if (!$request->query->has($name)) {
                continue;
            }
            $value = $request->query($name);
            abort_unless(is_scalar($value) && filter_var($value, FILTER_VALIDATE_INT) !== false
                && (int) $value >= $minimum && (int) $value <= $maximum, 404);
        }

        return $next($request);
    }
}
