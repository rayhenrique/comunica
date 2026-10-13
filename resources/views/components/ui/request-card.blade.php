@props(['event', 'triage' => false, 'conflictCount' => 0])
<li class="request-card" wire:key="{{ $triage ? 'triage' : 'history' }}-mobile-{{ $event->id }}">
    <h2 class="text-base font-semibold">{{ $event->title }}</h2>
    @if($triage)<p class="text-sm text-muted">{{ $event->user->department }}</p>@endif
    <p class="text-xs text-muted">{{ \App\Support\EventLabels::demand($event->demand_type) }}</p>
    <div>@if($triage)<x-ui.conflict-status :event="$event" :count="$conflictCount" />@else<x-ui.status :status="$event->status" />@endif</div>
    <dl class="grid grid-cols-2 gap-3">
        @if($triage)
            <div class="col-span-2"><dt>Quando</dt><dd>{{ $event->start_at->format('d/m/Y') }} · {{ $event->formattedStartTime() }}–{{ $event->formattedEndTime() }}</dd></div>
            <div class="col-span-2"><dt>Local</dt><dd>{{ $event->location }}</dd></div>
        @else
            <div><dt>Data original</dt><dd>{{ ($event->original_start_at ?? $event->start_at)->format('d/m/Y H:i') }}</dd></div>
            <div><dt>Data confirmada</dt><dd>{{ in_array($event->status, ['approved', 'rescheduled']) ? $event->start_at->format('d/m/Y H:i') : 'Não confirmada' }}</dd></div>
        @endif
    </dl>
    <x-ui.request-actions :event="$event" :triage="$triage" />
</li>
