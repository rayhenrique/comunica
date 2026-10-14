<?php

declare(strict_types=1);

namespace App\Support;

use LogicException;

final class AppVersion
{
    public const PATTERN = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/D';

    public static function current(): string
    {
        $version = config('version.current');
        if (!is_string($version) || preg_match(self::PATTERN, $version) !== 1) {
            throw new LogicException('Configure uma versão SemVer MAJOR.MINOR.PATCH válida.');
        }

        return $version;
    }
}
