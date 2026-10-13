@props(['event', 'count' => 0])
<x-ui.status :status="$event->status" />
@if($count)<p class="mt-2 text-xs font-semibold text-warning">⚠ {{ $count }} conflito(s) de agenda</p>
@elseif($event->status === 'pending')<p class="mt-2 text-xs text-muted">Sem conflitos</p>@endif
