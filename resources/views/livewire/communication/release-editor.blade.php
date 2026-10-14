<section class="mx-auto max-w-3xl" x-data="{dirty:false}" x-on:beforeunload.window="if(dirty){$event.preventDefault();$event.returnValue='';}" x-on:server-navigation.window="dirty=false">
    <p class="eyebrow">Comunicação · Notas de versão</p><h1 class="page-heading mt-3">{{ $releaseId ? 'Editar nota' : 'Criar nota' }}</h1>
    <p class="mt-3 text-muted">Versão implantada: {{ \App\Support\AppVersion::current() }}. Salvar não publica a nota.</p>
    <form wire:submit="save" novalidate x-on:input="dirty=true" class="panel mt-6 space-y-5 p-5">
        <x-ui.errors /><x-ui.field name="version" label="Versão" wire:model="version" required hint="Formato MAJOR.MINOR.PATCH. Uma nota por versão." />
        <x-ui.field name="title" label="Título" wire:model="title" maxlength="150" required />
        <x-ui.field name="summary" label="Resumo" as="textarea" wire:model="summary" maxlength="500" required hint="Texto curto para o modal após o login." />
        <x-ui.field name="content" label="Conteúdo" as="textarea" wire:model="content" required hint="Descreva novidades, melhorias e correções em linguagem simples. Apenas texto." />
        <div class="flex flex-wrap gap-3"><x-ui.button type="submit" intent="primary" wire:target="save">Salvar nota</x-ui.button><x-ui.button :href="route('releases.index')">Voltar</x-ui.button></div>
    </form>
</section>
