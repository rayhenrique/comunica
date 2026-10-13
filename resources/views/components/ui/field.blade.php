@props(['name', 'label', 'as' => 'input', 'type' => 'text', 'hint' => '', 'compact' => false])
@php($descriptionIds = trim(($hint !== '' ? $name.'-help ' : '').($errors->has($name) ? $name.'-error' : '')))
<div class="min-w-0 space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-semibold">{{ $label }}</label>
    @if($as === 'select')
        <select id="{{ $name }}" name="{{ $name }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" @if($descriptionIds !== '') aria-describedby="{{ $descriptionIds }}" @endif {{ $attributes->class(['field', 'field-compact' => $compact]) }}>{{ $slot }}</select>
    @elseif($as === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" @if($descriptionIds !== '') aria-describedby="{{ $descriptionIds }}" @endif {{ $attributes->class(['field resize-none', 'field-compact' => $compact]) }} x-data x-init="$el.style.height = Math.min($el.scrollHeight, 352) + 'px'" x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 352) + 'px'">{{ $slot }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" @if($descriptionIds !== '') aria-describedby="{{ $descriptionIds }}" @endif {{ $attributes->class(['field', 'field-compact' => $compact]) }} />
    @endif
    @if($hint !== '')<p id="{{ $name }}-help" class="field-help">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $name }}-error" class="field-error" role="alert">{{ $message }}</p>@enderror
</div>
