<footer class="page-width border-t border-border py-5 text-sm text-muted">
    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
        <p>Comunica · {{ \App\Support\AppVersion::current() }} · Agenda institucional <span class="block text-xs sm:mt-1">Horários de Brasília</span></p>
        <nav aria-label="Informações do sistema" class="flex flex-wrap items-center gap-x-5 gap-y-2">
            <a href="{{ route('about') }}" class="inline-flex min-h-11 items-center font-semibold text-primary hover:underline" @if(request()->routeIs('about')) aria-current="page" @endif>Sobre</a>
            <a href="{{ route('help') }}" class="inline-flex min-h-11 items-center font-semibold text-primary hover:underline">Ajuda</a>
            <a href="{{ route('news') }}" class="inline-flex min-h-11 items-center font-semibold text-primary hover:underline">Novidades</a>
            <span>Desenvolvido por <a href="https://kltecnologia.com" class="inline-flex min-h-11 items-center font-semibold text-primary hover:underline">KL Tecnologia</a></span>
        </nav>
    </div>
</footer>
