<section wire:poll.30s.visible x-on:validation-failed.window="$nextTick(() => document.querySelector('dialog[open] [aria-invalid=true]')?.focus())">
    <p class="eyebrow">Assessoria de Comunicação · Moderação</p>
    <div class="mt-2 flex flex-wrap items-end justify-between gap-4"><div><h1 class="page-heading">Triagem de pautas</h1><p class="mt-3 text-muted">Analise os pedidos, confira os conflitos e organize a agenda institucional.</p></div><x-ui.button :href="url('/calendario')">Ver calendário <span aria-hidden="true">↗</span></x-ui.button></div>
    <div><x-ui.feedback :message="$feedback" /></div>
    <div class="panel mt-5">
        <div class="grid grid-cols-2 items-end gap-3 border-b border-border p-4 lg:grid-cols-3 xl:grid-cols-6">
            <x-ui.field compact name="status" label="Situação" as="select" wire:model.live="status"><option value="">Todos</option>@foreach(\App\Support\EventLabels::statuses() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</x-ui.field>
            <x-ui.field compact name="department" label="Setor" as="select" wire:model.live="department"><option value="">Todos os setores</option>@foreach($departments as $departmentName)<option value="{{ $departmentName }}">{{ $departmentName }}</option>@endforeach</x-ui.field>
            <x-ui.field compact name="demand_type" label="Demanda" as="select" wire:model.live="demand_type"><option value="">Todas</option>@foreach(\App\Support\EventLabels::demands() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</x-ui.field>
            <x-ui.field compact name="from" label="A partir de" type="date" wire:model.live="from" />
            <x-ui.field compact name="to" label="Até" type="date" wire:model.live="to" />
            <div><x-ui.button wire:click="clearFilters">Limpar filtros</x-ui.button></div>
        </div>
        <div class="hidden overflow-x-auto md:block"><table class="data-table"><caption class="sr-only">Solicitações de todos os setores</caption><thead><tr><th scope="col">Pauta / setor</th><th scope="col">Quando / local</th><th scope="col">Demanda</th><th scope="col">Situação / alertas</th><th scope="col">Decisão</th></tr></thead><tbody>
            @forelse($requests as $event)<tr wire:key="triage-{{ $event->id }}"><td><p class="max-w-64 break-words font-semibold">{{ $event->title }}</p><p class="mt-1 text-xs text-muted">{{ $event->user->department }}</p></td><td><p class="whitespace-nowrap">{{ $event->start_at->format('d/m/Y') }} · {{ $event->formattedStartTime() }}–{{ $event->formattedEndTime() }}</p><p class="mt-1 max-w-52 break-words text-xs text-muted">{{ $event->location }}</p></td><td>{{ \App\Support\EventLabels::demand($event->demand_type) }}</td><td><x-ui.conflict-status :event="$event" :count="$conflictCounts[$event->id]" /></td><td><x-ui.request-actions :event="$event" triage /></td></tr>
            @empty<tr><td colspan="5" class="py-12 text-center"><h2 class="text-lg font-semibold">Nenhuma pauta neste filtro</h2><p class="mt-2 text-muted">Altere o período, setor ou status para consultar outras solicitações.</p></td></tr>@endforelse
        </tbody></table></div>
        <ul class="md:hidden" aria-label="Solicitações de todos os setores">
            @forelse($requests as $event)<x-ui.request-card :event="$event" triage :conflict-count="$conflictCounts[$event->id]" />
            @empty<li class="p-5"><h2 class="font-semibold">Nenhuma pauta neste filtro</h2><p class="mt-2 text-sm text-muted">Altere o período, setor ou status para consultar outras solicitações.</p></li>@endforelse
        </ul>
        <x-ui.pagination :paginator="$requests" />
    </div>
    <x-ui.dialog model="reviewOpen" title="Análise da pauta" drawer>
        @if($this->selectedEvent)
            <p class="mb-4 text-xs font-semibold text-muted">{{ $this->selectedEvent->user->department }} · {{ $this->selectedEvent->user->name }}</p>
            <x-ui.event-details :event="$this->selectedEvent" />
            @if($this->selectedEvent->status === 'pending')
                <section class="mt-6 border-t border-border pt-5">
                    @if($this->conflicts->isNotEmpty())
                        <div role="status" class="mb-5 rounded-control border border-warning/40 bg-background p-4"><h3 class="text-sm font-bold text-warning">Atenção: conflito de agenda</h3><p class="mt-1 text-xs text-muted">O alerta não impede a aprovação. Confira as pautas coincidentes:</p><ul class="mt-3 space-y-2">@foreach($this->conflicts as $conflict)<li class="break-words text-sm"><strong>{{ $conflict->title }}</strong><br>{{ $conflict->start_at->format('d/m H:i') }}–{{ $conflict->formattedEndTime() }} · {{ $conflict->location }}</li>@endforeach</ul></div>
                    @else <p class="mb-5 rounded-control bg-background p-3 text-sm text-success">Nenhum conflito encontrado para este horário.</p>@endif
                    <form id="moderation-form" wire:submit="moderate" novalidate class="space-y-4">
                        <x-ui.errors />
                        <x-ui.field compact name="decision" label="Decisão" as="select" wire:model.live="decision"><option value="approve">Aprovar</option><option value="reschedule">Reagendar e aprovar</option><option value="reject">Recusar</option></x-ui.field>
                        @if($decision === 'reschedule')<x-ui.field compact name="start_at" label="Nova data e hora" type="datetime-local" wire:model.live="start_at" required hint="Horário de Brasília. O reagendamento já confirma a nova agenda." />@endif
                        @if($decision !== 'approve')<x-ui.field compact name="moderation_notes" label="Justificativa" as="textarea" wire:model="moderation_notes" required :hint="$decision === 'reject' ? 'Informe pelo menos 10 caracteres.' : 'Explique o motivo do reagendamento.'" />@endif
                        <x-ui.feedback :message="$feedback" />
                    </form>
                </section>
            @endif
        @endif
        <x-slot:footer>
            <div class="flex flex-wrap gap-3"><x-ui.button x-on:click="open = false">Voltar à triagem</x-ui.button>
                @if($this->selectedEvent?->status === 'pending')<x-ui.button type="submit" form="moderation-form" :intent="$decision === 'reject' ? 'danger' : 'primary'" wire:target="moderate">{{ match($decision) { 'reschedule' => 'Reagendar e aprovar', 'reject' => 'Recusar solicitação', default => 'Aprovar solicitação' } }}</x-ui.button>@endif
            </div>
        </x-slot:footer>
    </x-ui.dialog>
</section>
