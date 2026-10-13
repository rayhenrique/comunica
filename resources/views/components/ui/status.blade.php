@props(['status'])
<span class="status" data-status="{{ $status }}">{{ \App\Support\EventLabels::status($status) }}</span>
