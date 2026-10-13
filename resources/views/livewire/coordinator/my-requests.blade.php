<section>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">Coordenador · Histórico</p><h1 class="page-heading mt-2">Minhas solicitações</h1><p class="mt-3 text-muted">Acompanhe as datas, o status e os pareceres da Comunicação.</p></div>
        <x-ui.button intent="primary" :href="url('/solicitacoes/nova')">Nova solicitação <span aria-hidden="true">+</span></x-ui.button>
    </div>
    <div class="my-5"><x-ui.feedback :message="$feedback" /></div>
    <div class="panel">
        <div class="flex flex-wrap items-center gap-4 border-b border-border p-5">
            <div class="w-full sm:w-60"><x-ui.field name="status" label="Filtrar por status" as="select" wire:model.live="status"><option value="">Todos os status</option>@foreach(\App\Support\EventLabels::statuses() as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</x-ui.field></div>
            @if($status !== '')<x-ui.button wire:click="$set('status', '')">Limpar filtro</x-ui.button>@endif
        </div>
        <div class="overflow-x-auto">
            <table class="data-table min-w-190"><caption class="sr-only">Histórico exclusivo das suas solicitações</caption><thead><tr><th scope="col">Evento / demanda</th><th scope="col">Data original</th><th scope="col">Data confirmada</th><th scope="col">Status</th><th scope="col">Ações</th></tr></thead>
                <tbody>@forelse($requests as $event)<tr wire:key="request-{{ $event->id }}">
                    <td><p class="max-w-64 break-words font-semibold">{{ $event->title }}</p><p class="mt-1 text-xs text-muted">{{ \App\Support\EventLabels::demand($event->demand_type) }}</p></td>
                    <td class="whitespace-nowrap">{{ ($event->original_start_at ?? $event->start_at)->format('d/m/Y') }}<span class="mt-1 block font-mono text-xs text-muted">{{ ($event->original_start_at ?? $event->start_at)->format('H:i') }}</span></td>
                    <td class="whitespace-nowrap">{{ in_array($event->status, ['approved','rescheduled']) ? $event->start_at->format('d/m/Y H:i') : '—' }}</td>
                    <td><x-ui.status :status="$event->status" /></td>
                    <td><div class="flex flex-wrap gap-2"><x-ui.button wire:click="openDetails({{ $event->id }})" aria-label="Detalhes de {{ $event->title }}">Detalhes</x-ui.button>@can('cancel',$event)<x-ui.button wire:click="askCancel({{ $event->id }})" aria-label="Cancelar {{ $event->title }}">Cancelar</x-ui.button>@endcan</div></td>
                </tr>@empty<tr><td colspan="5" class="py-12 text-center"><h2 class="text-lg font-semibold">{{ $status ? 'Nenhuma solicitação com este status' : 'Você ainda não criou uma solicitação' }}</h2><p class="mt-2 text-muted">{{ $status ? 'Limpe o filtro para consultar seu histórico completo.' : 'Crie seu primeiro pedido de apoio à Comunicação.' }}</p></td></tr>@endforelse</tbody>
            </table>
        </div>
        <x-ui.pagination :paginator="$requests" />
    </div>
    <x-ui.dialog model="detailOpen" title="Detalhes da solicitação" drawer>
        @if($this->selectedEvent)<x-ui.event-details :event="$this->selectedEvent" />@endif
    </x-ui.dialog>
    <x-ui.dialog model="cancelOpen" title="Cancelar solicitação?">
        @if($this->selectedEvent)<p class="break-words font-semibold">{{ $this->selectedEvent->title }}</p>@endif
        <p class="mt-3 text-sm text-muted">O pedido será cancelado e permanecerá no histórico. Para solicitar apoio novamente, será necessário criar uma nova solicitação.</p>
        <x-ui.errors />
        <x-slot:footer><div class="flex flex-wrap gap-3"><x-ui.button x-on:click="open = false">Manter solicitação</x-ui.button><x-ui.button intent="danger" wire:click="cancel" wire:target="cancel">Cancelar solicitação</x-ui.button></div></x-slot:footer>
    </x-ui.dialog>
</section>
