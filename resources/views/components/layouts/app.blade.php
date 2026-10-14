@props(['title' => 'Painel interno'])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Comunica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) @livewireStyles
</head>
<body>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <header class="border-b border-border bg-surface">
        <div class="page-width flex flex-wrap items-center justify-between gap-4 py-5">
            <a href="{{ url('/calendario') }}" class="brand text-2xl font-bold text-primary">comunica<span class="ml-1 inline-block h-2 w-2 rounded-full bg-primary" aria-hidden="true"></span></a>
            <div class="flex min-w-0 max-w-full items-center gap-3 text-sm"><span class="min-w-0 break-words [overflow-wrap:anywhere]">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ url('/logout') }}" novalidate>@csrf<x-ui.button type="submit">Sair</x-ui.button></form>
            </div>
        </div>
        <nav aria-label="Navegação do painel" class="page-width flex flex-wrap gap-2 pb-4">
            @can('create', \App\Models\EventRequest::class)
                <a class="nav-link" href="{{ url('/solicitacoes') }}" @if(request()->is('solicitacoes')) aria-current="page" @endif>Minhas solicitações</a>
                <a class="nav-link" href="{{ url('/solicitacoes/nova') }}" @if(request()->is('solicitacoes/nova')) aria-current="page" @endif>Nova solicitação</a>
            @endcan
            @can('moderate', \App\Models\EventRequest::class)<a class="nav-link" href="{{ url('/comunicacao/triagem') }}" @if(request()->is('comunicacao/triagem')) aria-current="page" @endif>Triagem de pautas</a>@endcan
            <a class="nav-link" href="{{ url('/calendario') }}">Calendário institucional <span aria-hidden="true">↗</span></a>
            <a class="nav-link" href="{{ route('about') }}">Sobre</a>
            <a class="nav-link" href="{{ route('help') }}">Ajuda</a>
            <a class="nav-link" href="{{ route('news') }}">Novidades</a>
            @can('viewAny', \App\Models\Release::class)<a class="nav-link" href="{{ route('releases.index') }}">Administrar novidades</a>@endcan
        </nav>
    </header>
    <x-ui.network-alert />
    <main id="conteudo" class="page-width py-9">{{ $slot }}</main>
    <x-ui.site-footer />
    @can('acknowledgeRelease', \App\Models\User::class)<livewire:release-notice />@endcan
    @livewireScripts
</body>
</html>
