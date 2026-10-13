@props(['name', 'label', 'as' => 'input', 'type' => 'text', 'hint' => ''])
<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-semibold">{{ $label }}</label>
    @if($as === 'select')
        <select id="{{ $name }}" name="{{ $name }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" aria-describedby="{{ $name }}-help {{ $name }}-error" {{ $attributes->class(['field']) }}>{{ $slot }}</select>
    @elseif($as === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" aria-describedby="{{ $name }}-help {{ $name }}-error" {{ $attributes->class(['field resize-none']) }} x-data x-init="$el.style.height = Math.min($el.scrollHeight, 352) + 'px'" x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 352) + 'px'">{{ $slot }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" aria-describedby="{{ $name }}-help {{ $name }}-error" {{ $attributes->class(['field']) }} />
    @endif
    <p id="{{ $name }}-help" class="field-help">{{ $hint }}</p>
    <p id="{{ $name }}-error" class="field-error" @if($errors->has($name)) role="alert" @endif>@error($name) {{ $message }} @enderror</p>
</div>
