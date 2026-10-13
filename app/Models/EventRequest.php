<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class EventRequest extends Model
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'demand_type',
        'location',
        'target_audience',
        'description',
        'start_at',
        'duration_minutes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'original_start_at' => 'datetime',
            'moderated_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * @param Builder<static> $query
     * @return Builder<static>
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->whereIn('status', ['approved', 'rescheduled']);
    }

    /**
     * Filtra interseção com o intervalo [início do mês, início do próximo mês).
     *
     * @param Builder<static> $query
     * @return Builder<static>
     */
    public function scopeInMonth(Builder $query, int $year, int $month): Builder
    {
        if (!checkdate($month, 1, $year)) {
            throw new InvalidArgumentException('Ano e mês devem formar uma data válida.');
        }

        $start = CarbonImmutable::create($year, $month, 1, 0, 0, 0, config('app.timezone'));

        return $query->where('start_at', '<', $start->addMonth()->toDateTimeString())
            ->where('end_at', '>', $start->toDateTimeString());
    }

    public function formattedStartTime(): ?string
    {
        return $this->start_at?->format('H:i');
    }

    public function formattedEndTime(): ?string
    {
        return $this->end_at?->format('H:i');
    }
}
