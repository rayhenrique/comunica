@props(['event', 'count' => 0])
<x-ui.status :status="$event->status" />
@if($count)<p class="mt-2 text-xs font-semibold text-warning">⚠ {{ trans_choice('messages.conflicts', $count, ['count' => $count]) }}</p>
@elseif($event->status === 'pending')<p class="mt-2 text-xs text-muted">Sem conflitos</p>@endif
