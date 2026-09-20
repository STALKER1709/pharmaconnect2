@props(['statut'])

@php
    // $statut : instance d'Enum avec label() et color()
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statut->color() }}">
    {{ $statut->label() }}
</span>
