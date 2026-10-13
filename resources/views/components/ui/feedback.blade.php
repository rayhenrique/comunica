@props(['message' => ''])
<div class="min-h-8" aria-live="polite" aria-atomic="true">
    @if($message ?: session('status')) <p role="status" class="rounded-control border border-success/30 bg-surface px-4 py-3 text-sm text-success">{{ $message ?: session('status') }}</p> @endif
    <p wire:offline class="rounded-control border border-danger/30 bg-surface px-4 py-3 text-sm text-danger">Sem conexão. Seus dados continuam nesta tela; reconecte para tentar novamente.</p>
    <p wire:loading.delay class="text-sm text-muted"><span class="spinner" aria-hidden="true"></span> Atualizando…</p>
</div>
