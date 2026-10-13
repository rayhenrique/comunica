<section class="mx-auto grid max-w-4xl gap-6 md:grid-cols-2 md:gap-12 md:py-6">
    <div class="self-center">
        <p class="eyebrow">Assessoria de Comunicação</p>
        <h1 class="mt-4 text-4xl font-bold leading-tight">Uma agenda.<br>Todos os setores.</h1>
        <p class="mt-5 max-w-sm text-muted">Centralize suas solicitações e acompanhe cada decisão, do primeiro pedido à pauta confirmada.</p>
        <a class="mt-8 inline-flex text-sm font-semibold text-primary hover:underline" href="{{ url('/calendario') }}">Consultar calendário público <span class="ml-2" aria-hidden="true">→</span></a>
    </div>
    <div class="panel p-6 md:p-8">
        <h2 class="text-2xl font-bold">Acesso interno</h2>
        <p class="mt-2 text-sm text-muted">Entre com o e-mail e a senha do seu setor.</p>
        <form wire:submit="login" novalidate class="mt-6 space-y-4">
            <x-ui.errors />
            <x-ui.field name="email" label="E-mail" type="email" wire:model="email" autocomplete="username" required />
            <div x-data="{ showPassword: false }">
                <x-ui.field name="password" label="Senha" type="password" x-bind:type="showPassword ? 'text' : 'password'" wire:model="password" autocomplete="current-password" required />
                <button type="button" class="inline-flex min-h-11 items-center rounded-control px-2 text-sm font-semibold text-primary hover:underline" x-on:click="showPassword = !showPassword" x-bind:aria-pressed="showPassword" x-text="showPassword ? 'Ocultar senha' : 'Mostrar senha'">Mostrar senha</button>
            </div>
            <x-ui.feedback />
            <x-ui.button type="submit" intent="primary" class="w-full" wire:target="login">Entrar <span aria-hidden="true">→</span></x-ui.button>
        </form>
        <p class="mt-5 text-xs leading-5 text-muted">Ainda não tem acesso? Solicite suas credenciais à equipe responsável pelo sistema.</p>
    </div>
</section>
