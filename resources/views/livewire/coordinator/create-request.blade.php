<section class="mx-auto max-w-4xl" x-data="{ dirty: false, leaveOpen: false, dateValue: @entangle('form.start_at'), duration: @entangle('form.duration_minutes'), get endPreview() { if (!this.dateValue || !Number(this.duration)) return 'Defina o início e a duração'; const date = new Date(this.dateValue); if (isNaN(date)) return 'Confira a data de início'; date.setMinutes(date.getMinutes() + Number(this.duration)); return date.toLocaleString('pt-BR', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'}); } }" x-on:beforeunload.window="if(dirty) { $event.preventDefault(); $event.returnValue = ''; }" x-on:form-saved.window="dirty = false" x-on:server-navigation.window="dirty = false" x-on:validation-failed.window="$nextTick(() => document.querySelector('[aria-invalid=true]')?.focus())">
    <p class="eyebrow">Coordenador · Novo pedido</p>
    <h1 class="page-heading mt-2">Nova solicitação</h1>
    <p class="mt-3 text-muted">Conte à Comunicação o que seu evento precisa. Envie com pelo menos 48 horas corridas de antecedência.</p>
    <form wire:submit="save" novalidate x-on:input="dirty = true" class="panel mt-7 p-5 md:p-8">
        <x-ui.errors />
        <div class="grid gap-x-6 gap-y-5 md:grid-cols-2">
            <div class="md:col-span-2"><x-ui.field name="form.title" label="Título do evento" wire:model.blur="form.title" maxlength="150" required placeholder="Ex.: Encontro de formação dos servidores" /></div>
            <x-ui.field name="form.demand_type" label="Tipo de demanda" as="select" wire:model.blur="form.demand_type" required>
                @foreach(\App\Support\EventLabels::demands() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
            </x-ui.field>
            <x-ui.field name="form.start_at" label="Data e hora de início" type="datetime-local" wire:model.blur="form.start_at" x-model="dateValue" required hint="Horário de Brasília. Mínimo de 48 horas corridas." />
            <div class="min-w-0"><x-ui.field name="form.duration_minutes" label="Duração estimada (minutos)" type="number" wire:model.blur="form.duration_minutes" x-model="duration" min="1" max="65535" required />
            <div class="mt-2 rounded-control bg-background px-3 py-2"><p class="text-xs font-semibold text-muted">Término previsto</p><p class="mt-1 font-mono text-base" x-text="endPreview"></p><p class="mt-2 text-xs text-muted">Calculado automaticamente a partir da duração.</p></div></div>
            <x-ui.field name="form.location" label="Local do evento" wire:model.blur="form.location" maxlength="255" required />
            <x-ui.field name="form.target_audience" label="Público-alvo" wire:model.blur="form.target_audience" maxlength="255" required />
            <div class="md:col-span-2"><x-ui.field name="form.description" label="Descrição do pedido" as="textarea" wire:model.blur="form.description" hint="Descreva o objetivo, o contexto e o apoio necessário. Apenas texto." /></div>
        </div>
        <x-ui.feedback />
        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-border pt-5">
            <button type="button" class="button" x-on:click="if(dirty) { $dispatch('open-local-dialog', { name: 'leaveOpen' }); } else { window.location.href = '{{ url('/solicitacoes') }}'; }">Voltar à lista</button>
            <x-ui.button intent="primary" type="submit" wire:target="save">Criar solicitação <span aria-hidden="true">→</span></x-ui.button>
        </div>
    </form>
    <x-ui.dialog model="leaveOpen" title="Sair sem criar a solicitação?" local>
        <p class="text-muted">Os dados preenchidos não serão salvos.</p>
        <x-slot:footer><div class="flex flex-wrap gap-3"><x-ui.button x-on:click="open = false">Continuar preenchendo</x-ui.button><x-ui.button intent="danger" x-on:click="dirty = false; window.location.href = '{{ url('/solicitacoes') }}'">Sair sem salvar</x-ui.button></div></x-slot:footer>
    </x-ui.dialog>
</section>
