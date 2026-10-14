<section>
    <p class="eyebrow">Comunicação · Publicações</p><div class="mt-3 flex flex-wrap items-center justify-between gap-4"><h1 class="page-heading">Administrar novidades</h1><x-ui.button intent="primary" :href="route('releases.create')">Criar nota</x-ui.button></div>
    <p class="mt-3 text-muted">Versão do código implantado: {{ \App\Support\AppVersion::current() }}. Publicar uma nota não altera a versão do sistema.</p>
    <x-ui.feedback :message="$feedback" />
    <ul class="panel mt-5 divide-y divide-border">
        @forelse($releases as $release)<li class="p-5" wire:key="release-{{ $release->id }}"><div class="flex flex-col items-start justify-between gap-4 sm:flex-row"><div class="min-w-0 flex-1"><p class="text-sm text-primary">Versão {{ $release->version }} · {{ $release->state === 'published' ? 'Publicada' : 'Rascunho' }}</p><h2 class="mt-2 break-words text-xl font-bold">{{ $release->title }}</h2><p class="mt-2 break-words text-muted">{{ $release->summary }}</p></div><div class="flex flex-wrap gap-2"><x-ui.button :href="route('releases.edit', $release->id)">Editar</x-ui.button><x-ui.button :href="route('releases.history', $release)">Histórico</x-ui.button>@if($release->state === 'draft')<x-ui.button wire:click="askPublish({{ $release->id }})">Publicar</x-ui.button>@endif</div></div></li>
        @empty<li class="p-5"><h2 class="font-semibold">Nenhuma nota cadastrada</h2><p class="mt-2 text-muted">Crie a nota da versão implantada e publique após revisar o conteúdo.</p></li>@endforelse
    </ul><x-ui.pagination noun="notas" :paginator="$releases" />
    <x-ui.dialog model="publishOpen" title="Publicar nota de versão?">
        <x-ui.errors />@if($selected)<p class="font-semibold">{{ $selected->version }} · {{ $selected->title }}</p>@endif
        <p class="mt-3 text-muted">A nota ficará pública. O resumo será apresentado aos usuários no primeiro login, até registrarem a leitura.</p>
        <x-slot:footer><div class="flex flex-wrap gap-3"><x-ui.button x-on:click="open=false">Voltar</x-ui.button><x-ui.button intent="primary" wire:click="publishSelected">Confirmar publicação</x-ui.button></div></x-slot:footer>
    </x-ui.dialog>
</section>
