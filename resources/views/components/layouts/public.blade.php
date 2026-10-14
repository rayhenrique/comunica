@props(['title' => 'Calendário institucional'])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Comunica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="border-b border-border bg-surface">
        <div class="page-width flex min-h-20 flex-wrap items-center justify-between gap-3 py-4">
            <a href="{{ url('/calendario') }}" class="brand text-2xl font-bold tracking-tight text-primary">comunica<span class="ml-1 inline-block h-2 w-2 rounded-full bg-primary" aria-hidden="true"></span></a>
            <nav aria-label="Navegação principal" class="flex flex-wrap items-center gap-2">
                <a class="nav-link inline-flex min-h-11 items-center" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>Sobre</a>
                @auth <x-ui.button :href="auth()->user()->isCommunicationAdmin() ? url('/comunicacao/triagem') : url('/solicitacoes')">Meu painel</x-ui.button>
                @else <x-ui.button :href="url('/login')">Acesso interno <span aria-hidden="true">↗</span></x-ui.button> @endauth
            </nav>
        </div>
    </header>
    <x-ui.network-alert />
    <main id="conteudo" class="page-width py-9 md:py-12">{{ $slot }}</main>
    <x-ui.site-footer />
    @can('acknowledgeRelease', \App\Models\User::class)<livewire:release-notice />@endcan
    @livewireScripts
</body>
</html>
