@props(['code', 'title', 'message'])
<x-layouts.public :title="$title"><div class="mx-auto max-w-xl py-8"><p class="eyebrow">{{ $code }} · Comunica</p><h1 class="page-heading mt-3">{{ $title }}</h1><p class="mt-4 leading-7 text-muted">{{ $message }}</p><x-ui.button class="mt-6" :href="route('calendar')">Ir para o calendário</x-ui.button></div></x-layouts.public>
