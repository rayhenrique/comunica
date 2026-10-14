<div>
    <x-ui.dialog model="noticeOpen" title="Novidades do Comunica">
        @if($release)<p class="eyebrow">Versão {{ $version }}</p><h3 class="mt-3 break-words text-2xl font-bold">{{ $release->title }}</h3><p class="mt-4 whitespace-pre-line break-words leading-7 text-muted">{{ $release->summary }}</p>@endif
        <x-ui.errors /><p class="mt-5 text-sm text-muted">Você pode consultar novamente este conteúdo em Novidades.</p>
        <x-slot:footer><div class="flex flex-wrap gap-3"><x-ui.button intent="primary" wire:click="dismiss">Entendi</x-ui.button><x-ui.button :href="route('news')">Ver novidades</x-ui.button></div></x-slot:footer>
    </x-ui.dialog>
</div>
