<section wire:poll.60s.visible>
    <div class="max-w-2xl"><p class="eyebrow">Agenda aberta à comunidade</p><h1 class="mt-3 text-4xl font-bold tracking-tight md:text-5xl">Calendário institucional</h1><p class="mt-4 text-base leading-7 text-muted">Acompanhe as ações confirmadas. Selecione um dia para consultar horários, locais e público-alvo.</p></div>
    <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
        <div><h2 class="text-2xl font-bold md:text-3xl">{{ ucfirst($monthStart->locale('pt_BR')->translatedFormat('F')) }} <span class="font-normal text-muted">{{ $year }}</span></h2><p class="mt-2 text-sm text-muted">{{ $this->events->count() }} pauta(s) confirmada(s) neste mês</p></div>
        <div class="flex gap-2"><x-ui.button wire:click="previousMonth" aria-label="Mês anterior"><span aria-hidden="true">←</span></x-ui.button><x-ui.button wire:click="today">Mês atual</x-ui.button><x-ui.button wire:click="nextMonth" aria-label="Próximo mês"><span aria-hidden="true">→</span></x-ui.button></div>
    </div>
    <div class="my-3"><x-ui.feedback /></div>
    <div class="panel hidden overflow-hidden md:block">
        <div class="grid grid-cols-7 border-b border-border bg-background">@foreach(['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $weekday)<div class="px-3 py-3 text-center text-xs font-semibold text-muted">{{ $weekday }}</div>@endforeach</div>
        <div class="grid grid-cols-7">
            @foreach($this->days as $day)
                @if($day['inMonth'])<button type="button" class="calendar-cell" wire:key="day-{{ $day['date']->format('Y-m-d') }}" wire:click="selectDay('{{ $day['date']->format('Y-m-d') }}')" aria-label="{{ $day['date']->format('d/m/Y') }}, {{ $day['events']->count() }} evento(s)" aria-pressed="{{ $selectedDay === $day['date']->format('Y-m-d') ? 'true' : 'false' }}" @if($day['date']->isToday()) aria-current="date" @endif>
                    <span class="mb-1 inline-flex size-7 items-center justify-center self-start rounded-full text-sm font-semibold {{ $day['date']->isToday() ? 'bg-primary text-surface' : '' }}">{{ $day['date']->day }}</span>
                    @foreach($day['events']->take(2) as $event)<span class="event-strip" data-type="{{ $event->demand_type }}"><span class="font-mono">{{ $event->formattedStartTime() }}</span> {{ $event->title }}</span>@endforeach
                    @if($day['events']->count() > 2)<span class="text-xs font-semibold text-primary">+ {{ $day['events']->count() - 2 }} pauta(s)</span>@endif
                </button>@else<div class="calendar-cell bg-background text-border"><span class="text-sm">{{ $day['date']->day }}</span></div>@endif
            @endforeach
        </div>
    </div>
    <div class="space-y-4 md:hidden">
        <div class="panel p-4"><x-ui.field name="selectedDay" label="Consultar um dia" type="date" :value="$selectedDay" :min="$monthStart->format('Y-m-d')" :max="$monthStart->endOfMonth()->format('Y-m-d')" wire:change="selectDay($event.target.value)" /></div>
        @forelse(collect($this->days)->filter(fn($day) => $day['inMonth'] && $day['events']->isNotEmpty()) as $day)
            <button type="button" wire:click="selectDay('{{ $day['date']->format('Y-m-d') }}')" class="panel flex w-full items-start gap-4 p-4 text-left hover:border-primary" aria-label="Ver eventos de {{ $day['date']->format('d/m/Y') }}"><span class="min-w-12 text-center"><strong class="block text-2xl">{{ $day['date']->day }}</strong><span class="text-xs text-muted">{{ $day['date']->locale('pt_BR')->translatedFormat('D') }}</span></span><span class="min-w-0 flex-1">@foreach($day['events'] as $event)<span class="mb-2 block break-words"><span class="block font-semibold">{{ $event->title }}</span><span class="mt-1 block text-xs text-muted">{{ $event->formattedStartTime() }}–{{ $event->formattedEndTime() }} · {{ $event->location }}</span></span>@endforeach</span><span class="text-primary" aria-hidden="true">→</span></button>
        @empty<div class="panel p-7 text-center"><h3 class="text-lg font-semibold">Nenhuma pauta confirmada neste mês</h3><p class="mt-2 text-sm text-muted">Consulte outro mês ou selecione um dia para ver sua agenda.</p></div>@endforelse
    </div>
    <div class="mt-5 flex flex-wrap gap-5 text-xs text-muted"><span><span class="mr-2 inline-block h-3 border-l-2 border-primary" aria-hidden="true"></span>Cobertura</span><span><span class="mr-2 inline-block h-3 border-l-2 border-muted" aria-hidden="true"></span>Criação de arte</span><span><span class="mr-2 inline-block h-3 border-l-2 border-warning" aria-hidden="true"></span>Cobertura e arte</span><span class="ml-auto">Somente pautas aprovadas ou reagendadas</span></div>
    @if($this->events->isEmpty())<p class="mt-4 hidden text-sm text-muted md:block">Nenhuma pauta confirmada neste mês. Use a navegação para consultar outros meses.</p>@endif
    <x-ui.dialog model="dayOpen" :title="$selectedDay ? Carbon\CarbonImmutable::parse($selectedDay)->locale('pt_BR')->translatedFormat('d \d\e F') : 'Eventos do dia'" drawer>
        <p class="mb-5 text-xs text-muted">Agenda confirmada · Horários de Brasília</p>
        @forelse($this->dayEvents as $event)<article class="mb-5 border-b border-border pb-5 last:mb-0 last:border-0"><p class="font-mono text-sm text-primary">{{ $event->start_at->format('d/m H:i') }}–{{ $event->end_at->format('d/m H:i') }}</p><h3 class="mt-2 break-words text-xl font-bold">{{ $event->title }}</h3><p class="mt-2 text-xs font-semibold text-muted">{{ \App\Support\EventLabels::demand($event->demand_type) }}</p><dl class="mt-4 space-y-3 text-sm"><div><dt class="font-semibold">Local</dt><dd class="mt-1 break-words text-muted">{{ $event->location }}</dd></div><div><dt class="font-semibold">Público-alvo</dt><dd class="mt-1 break-words text-muted">{{ $event->target_audience }}</dd></div></dl></article>
        @empty<div class="py-6"><h3 class="text-xl font-semibold">Nenhum evento confirmado neste dia</h3><p class="mt-3 text-sm text-muted">Escolha outro dia no calendário.</p></div>@endforelse
    </x-ui.dialog>
</section>
