@props(['message' => ''])
<div class="feedback-stack" aria-live="polite" aria-atomic="true">
    @if($message ?: session('status')) <p role="status" class="rounded-control border border-success/30 bg-surface px-4 py-3 text-sm text-success">{{ $message ?: session('status') }}</p> @endif
    <p wire:offline class="rounded-control border border-danger/30 bg-surface px-4 py-3 text-sm text-danger">Sem conexão. Seus dados continuam nesta tela; reconecte para tentar novamente.</p>
    <p wire:loading.delay class="sr-only">Atualizando…</p>
</div>
