@props(['intent' => 'neutral', 'type' => 'button', 'href' => null])
@php($classes = 'button '.match($intent) { 'primary' => 'button-primary', 'danger' => 'button-danger', default => '' })
@if($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }} wire:loading.attr="disabled" wire:loading.class="is-loading">{{ $slot }}</button>
@endif
