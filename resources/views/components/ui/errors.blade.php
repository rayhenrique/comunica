@if($errors->any())
    <div role="alert" class="mb-5 rounded-control border border-danger/30 bg-surface p-4 text-sm text-danger" x-data x-init="$nextTick(() => document.querySelector('[aria-invalid=true]')?.focus())">
        <p class="font-semibold">Confira os dados antes de continuar.</p>
        <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
