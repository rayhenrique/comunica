@props(['paginator', 'noun' => 'solicitações'])
<nav aria-label="Paginação" class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-4">
    <p class="text-sm text-muted">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} {{ $noun }}</p>
    <div class="flex min-w-0 flex-wrap items-center gap-2">
        <x-ui.button wire:click="previousPage" :disabled="$paginator->onFirstPage()" aria-label="Página anterior">Anterior</x-ui.button>
        <span class="text-sm" aria-current="page">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        <x-ui.button wire:click="nextPage" :disabled="!$paginator->hasMorePages()" aria-label="Próxima página">Próxima</x-ui.button>
    </div>
</nav>
