<div x-data="{ message: '', expired: false }" x-on:request-fault.window="message = $event.detail.message; expired = $event.detail.expired" x-on:request-recovered.window="message = ''" x-show="message" x-cloak class="page-width mt-5">
    <div role="alert" class="rounded-control border border-danger/40 bg-surface p-4 text-sm text-danger">
        <p x-text="message"></p>
        <a x-show="expired" href="{{ url('/login') }}" target="_blank" rel="noopener" class="mt-2 inline-block font-semibold underline">Entrar novamente em outra aba</a>
    </div>
</div>
