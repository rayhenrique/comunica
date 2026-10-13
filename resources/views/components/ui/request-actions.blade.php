@props(['event', 'triage' => false])
<div class="flex flex-wrap gap-2">
    @if($triage)
        <x-ui.button wire:click="openReview({{ $event->id }})" :aria-label="($event->status === 'pending' ? 'Analisar ' : 'Detalhes de ').$event->title">{{ $event->status === 'pending' ? 'Analisar' : 'Detalhes' }}</x-ui.button>
    @else
        <x-ui.button wire:click="openDetails({{ $event->id }})" :aria-label="'Detalhes de '.$event->title">Detalhes</x-ui.button>
        @can('cancel', $event)<x-ui.button wire:click="askCancel({{ $event->id }})" :aria-label="'Cancelar '.$event->title">Cancelar</x-ui.button>@endcan
    @endif
</div>
