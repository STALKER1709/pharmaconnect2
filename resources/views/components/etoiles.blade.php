@props(['note' => 0])
{{-- Étoiles Material Symbols pleines/demi/vides (maquettes fiche médicament et officine) --}}
@php $note = (float) $note; @endphp
<div {{ $attributes->merge(['class' => 'flex text-[#eab308]']) }}>
    @for ($i = 1; $i <= 5; $i++)
        @if ($note >= $i)
            <span class="material-symbols-outlined icon-fill" style="font-size: inherit">star</span>
        @elseif ($note >= $i - 0.5)
            <span class="material-symbols-outlined icon-fill" style="font-size: inherit">star_half</span>
        @else
            <span class="material-symbols-outlined text-outline-variant" style="font-size: inherit">star</span>
        @endif
    @endfor
</div>
