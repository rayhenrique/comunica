@props(['event'])
<div class="space-y-5">
    <div><x-ui.status :status="$event->status" /><h3 class="mt-3 break-words text-2xl font-bold">{{ $event->title }}</h3><p class="mt-2 text-sm text-muted">{{ \App\Support\EventLabels::demand($event->demand_type) }}</p></div>
    <dl class="grid gap-4 text-sm sm:grid-cols-2">
        <div><dt class="text-muted">Data solicitada</dt><dd class="mt-1 font-semibold">{{ ($event->original_start_at ?? $event->start_at)->format('d/m/Y H:i') }}</dd></div>
        <div><dt class="text-muted">Data confirmada</dt><dd class="mt-1 font-semibold">{{ in_array($event->status, ['approved','rescheduled']) ? $event->start_at->format('d/m/Y H:i') : 'Não confirmada' }}</dd></div>
        <div><dt class="text-muted">Horário previsto</dt><dd class="mt-1 font-mono">{{ $event->formattedStartTime() }}–{{ $event->formattedEndTime() }} · {{ $event->duration_minutes }} min</dd></div>
        <div><dt class="text-muted">Local</dt><dd class="mt-1 break-words">{{ $event->location }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-muted">Público-alvo</dt><dd class="mt-1 break-words">{{ $event->target_audience }}</dd></div>
    </dl>
    <div><h3 class="text-sm font-semibold">Briefing</h3><p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-muted">{{ $event->description ?: 'Nenhuma descrição informada.' }}</p></div>
    @if($event->moderation_notes)<div class="rounded-control bg-background p-4"><h3 class="text-sm font-semibold">Parecer da Comunicação</h3><p class="mt-2 whitespace-pre-line break-words text-sm leading-6">{{ $event->moderation_notes }}</p></div>@endif
    @if($event->moderated_at)<p class="text-xs text-muted">Decisão registrada em {{ $event->moderated_at->format('d/m/Y H:i') }} · Horário de Brasília</p>@endif
</div>
