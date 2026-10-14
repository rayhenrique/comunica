<section>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">Coordenador · Histórico</p><h1 class="page-heading mt-2">Minhas solicitações</h1><p class="mt-3 text-muted">Acompanhe as datas, o status e os pareceres da Comunicação.</p></div>
        <x-ui.button intent="primary" :href="url('/solicitacoes/nova')">Nova solicitação <span aria-hidden="true">+</span></x-ui.button>
    </div>
    <div><x-ui.feedback :message="$feedback" /></div>
    <div class="panel mt-5">
        <div class="flex flex-wrap items-end gap-3 border-b border-border p-4">
            <div class="w-full sm:w-60"><x-ui.field name="status" label="Filtrar por situação" as="select" wire:model.live="status"><option value="">Todos os status</option>@foreach(\App\Support\EventLabels::statuses() as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</x-ui.field></div>
            @if($status !== '')<x-ui.button wire:click="$set('status', '')">Limpar filtro</x-ui.button>@endif
        </div>
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table"><caption class="sr-only">Histórico exclusivo das suas solicitações</caption><thead><tr><th scope="col">Evento / demanda</th><th scope="col">Data original</th><th scope="col">Data confirmada</th><th scope="col">Situação</th><th scope="col">Ações</th></tr></thead>
                <tbody>@forelse($requests as $event)<tr wire:key="request-{{ $event->id }}">
                    <td><p class="max-w-64 break-words font-semibold">{{ $event->title }}</p><p class="mt-1 text-xs text-muted">{{ \App\Support\EventLabels::demand($event->demand_type) }}</p></td>
                    <td class="whitespace-nowrap">{{ ($event->original_start_at ?? $event->start_at)->format('d/m/Y') }}<span class="mt-1 block font-mono text-xs text-muted">{{ ($event->original_start_at ?? $event->start_at)->format('H:i') }}</span></td>
                    <td class="whitespace-nowrap">{{ in_array($event->status, ['approved','rescheduled']) ? $event->start_at->format('d/m/Y H:i') : '—' }}</td>
                    <td><x-ui.status :status="$event->status" /></td>
                    <td><x-ui.request-actions :event="$event" /></td>
                </tr>@empty<tr><td colspan="5" class="py-12 text-center"><h2 class="text-lg font-semibold">{{ $status ? 'Nenhuma solicitação com este status' : 'Você ainda não criou uma solicitação' }}</h2><p class="mt-2 text-muted">{{ $status ? 'Limpe o filtro para consultar seu histórico completo.' : 'Crie seu primeiro pedido de apoio à Comunicação.' }}</p></td></tr>@endforelse</tbody>
            </table>
        </div>
        <ul class="md:hidden" aria-label="Histórico exclusivo das suas solicitações">
            @forelse($requests as $event)<x-ui.request-card :event="$event" />
            @empty<li class="p-5"><h2 class="font-semibold">{{ $status ? 'Nenhuma solicitação com este status' : 'Você ainda não criou uma solicitação' }}</h2><p class="mt-2 text-sm text-muted">{{ $status ? 'Limpe o filtro para consultar seu histórico completo.' : 'Crie seu primeiro pedido de apoio à Comunicação.' }}</p></li>@endforelse
        </ul>
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
