<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EventRequestSeeder extends Seeder
{
    public function run(): void
    {
        $moderatorId = DB::table('users')->where('email', 'comunicacao@example.test')->value('id');
        $coordinatorIds = [
            DB::table('users')->where('email', 'educacao@example.test')->value('id'),
            DB::table('users')->where('email', 'saude@example.test')->value('id'),
        ];

        if ($moderatorId === null || in_array(null, $coordinatorIds, true)) {
            throw new RuntimeException('Execute UserSeeder antes de EventRequestSeeder.');
        }

        $statuses = ['pending', 'approved', 'rescheduled', 'rejected', 'cancelled'];
        $demandTypes = ['coverage', 'art', 'both'];
        $baseDate = now()->addWeek()->startOfDay()->setHour(9);

        for ($index = 0; $index < 10; $index++) {
            $userId = $coordinatorIds[$index % 2];
            $title = sprintf('Pauta de demonstração %02d', $index + 1);

            // Reexecuções preservam inclusive decisões e exclusões lógicas existentes.
            if (DB::table('event_requests')->where('user_id', $userId)->where('title', $title)->exists()) {
                continue;
            }

            $status = $statuses[$index % count($statuses)];
            $originalStart = $baseDate->copy()->addDays($index);
            $start = $status === 'rescheduled' ? $originalStart->copy()->addDay() : $originalStart;
            $duration = [30, 60, 90, 120][$index % 4];
            $moderated = in_array($status, ['approved', 'rescheduled', 'rejected'], true);

            DB::table('event_requests')->insert([
                'user_id' => $userId,
                'title' => $title,
                'demand_type' => $demandTypes[$index % count($demandTypes)],
                'location' => $index % 2 === 0 ? 'Auditório Municipal' : 'Centro de Saúde',
                'target_audience' => 'Servidores e comunidade',
                'description' => 'Dados fictícios para validação do ambiente local.',
                'start_at' => $start,
                'end_at' => $start->copy()->addMinutes($duration),
                'duration_minutes' => $duration,
                'original_start_at' => $status === 'rescheduled' ? $originalStart : null,
                'status' => $status,
                'moderated_by' => $moderated ? $moderatorId : null,
                'moderation_notes' => match ($status) {
                    'rescheduled' => 'Reagendado para adequação da agenda institucional.',
                    'rejected' => 'Recusado por indisponibilidade de recursos nesta data.',
                    default => null,
                },
                'moderated_at' => $moderated ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
