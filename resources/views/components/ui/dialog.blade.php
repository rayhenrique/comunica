@props(['model', 'title', 'drawer' => false, 'local' => false])
<div x-data="dialogPanel($wire, '{{ $model }}', @js($local))" x-on:open-local-dialog.window="if(@js($local) && $event.detail.name === '{{ $model }}') open = true">
    <dialog x-ref="dialog" wire:ignore.self class="ui-dialog {{ $drawer ? 'ui-drawer' : '' }} {{ isset($footer) ? 'dialog-with-footer' : '' }}" aria-modal="true" aria-labelledby="{{ $model }}-title" @cancel.prevent="open = false" @close="open = false">
        <header class="flex items-center justify-between gap-4 border-b border-border px-6 py-4">
            <h2 id="{{ $model }}-title" class="text-xl font-bold">{{ $title }}</h2>
            <x-ui.button x-on:click="open = false" aria-label="Fechar painel">Fechar</x-ui.button>
        </header>
        <div class="ui-dialog-body">{{ $slot }}</div>
        @isset($footer)<footer class="border-t border-border bg-surface px-6 py-4">{{ $footer }}</footer>@endisset
    </dialog>
</div>
