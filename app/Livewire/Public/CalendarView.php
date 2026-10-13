<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Actions\GetPublicCalendarEventsAction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Calendário institucional')]
class CalendarView extends Component
{
    #[Url]
    public int $year = 0;
    #[Url]
    public int $month = 0;
    #[Locked]
    public string $selectedDay = '';
    public bool $dayOpen = false;

    public function mount(): void
    {
        $this->year = $this->year ?: now()->year;
        $this->month = $this->month ?: now()->month;
        abort_unless($this->year >= 1000 && $this->year <= 9998 && $this->month >= 1 && $this->month <= 12, 404);
    }

    public function previousMonth(): void
    {
        $this->changeMonth(-1);
    }

    public function nextMonth(): void
    {
        $this->changeMonth(1);
    }

    public function today(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->dayOpen = false;
        $this->selectedDay = '';
        unset($this->events, $this->days, $this->dayEvents);
    }

    public function selectDay(string $date): void
    {
        if ($date === '') {
            $this->selectedDay = '';
            $this->dayOpen = false;
            unset($this->dayEvents);
            $this->resetErrorBag();
            return;
        }
        Validator::make(['selectedDay' => $date], ['selectedDay' => ['required', 'date_format:Y-m-d']])->validate();
        $day = CarbonImmutable::parse($date, config('app.timezone'));
        if ($day->year !== $this->year || $day->month !== $this->month) {
            throw \Illuminate\Validation\ValidationException::withMessages(['selectedDay' => 'Selecione um dia do mês exibido.']);
        }
        $this->resetErrorBag();
        $this->selectedDay = $date;
        $this->dayOpen = true;
        unset($this->dayEvents);
    }

    /** @return Collection<int, \App\Models\EventRequest> */
    #[Computed]
    public function events(): Collection
    {
        $this->monthStart();
        return app(GetPublicCalendarEventsAction::class)->execute($this->year, $this->month);
    }

    /** @return array<int, array<string, mixed>> */
    #[Computed]
    public function days(): array
    {
        $gridStart = $this->monthStart()->startOfWeek(CarbonImmutable::SUNDAY);
        $days = [];
        for ($index = 0; $index < 42; $index++) {
            $date = $gridStart->addDays($index);
            $days[] = ['date' => $date, 'inMonth' => $date->month === $this->month,
                'events' => $this->events->filter(fn ($event): bool => $event->start_at->lt($date->addDay()) && $event->end_at->gt($date))];
        }

        return $days;
    }

    /** @return Collection<int, \App\Models\EventRequest> */
    #[Computed]
    public function dayEvents(): Collection
    {
        if ($this->selectedDay === '') {
            return new Collection();
        }
        $date = CarbonImmutable::parse($this->selectedDay, config('app.timezone'));

        return $this->events->filter(fn ($event): bool => $event->start_at->lt($date->addDay()) && $event->end_at->gt($date));
    }

    private function changeMonth(int $offset): void
    {
        $date = $this->monthStart()->addMonths($offset);
        abort_unless($date->year >= 1000 && $date->year <= 9998, 422);
        $this->year = $date->year;
        $this->month = $date->month;
        $this->selectedDay = '';
        $this->dayOpen = false;
        unset($this->events, $this->days, $this->dayEvents);
    }

    private function monthStart(): CarbonImmutable
    {
        abort_unless($this->year >= 1000 && $this->year <= 9998 && $this->month >= 1 && $this->month <= 12, 404);
        return CarbonImmutable::create($this->year, $this->month, 1, 0, 0, 0, config('app.timezone'));
    }

    public function render(): View
    {
        return view('livewire.public.calendar-view', ['monthStart' => $this->monthStart()]);
    }
}
