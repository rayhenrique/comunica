<?php

declare(strict_types=1);

namespace App\Support;

class EventLabels
{
    /** @return array<string, string> */
    public static function statuses(): array
    {
        return ['pending' => 'Pendente', 'approved' => 'Aprovada', 'rescheduled' => 'Reagendada',
            'rejected' => 'Recusada', 'cancelled' => 'Cancelada'];
    }

    public static function status(string $status): string
    {
        return self::statuses()[$status] ?? 'Status desconhecido';
    }

    /** @return array<string, string> */
    public static function demands(): array
    {
        return ['coverage' => 'Cobertura', 'art' => 'Criação de arte', 'both' => 'Cobertura e arte'];
    }

    public static function demand(string $type): string
    {
        return self::demands()[$type] ?? 'Tipo desconhecido';
    }
}
