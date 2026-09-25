@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'icone' => null, 'aide' => null, 'id' => null, 'sac' => 'default'])
{{-- Champ de formulaire — style des maquettes (validation de commande) --}}
@php
    $id = $id ?? $name;
    $erreur = $errors->getBag($sac)->first(str_replace(['[', ']'], ['.', ''], $name));
@endphp
<div>
    @if ($label)
        <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="{{ $id }}">{{ $label }}</label>
    @endif
    <div class="relative">
        @if ($icone)
            <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-[18px] pointer-events-none">{{ $icone }}</span>
        @endif
        @if ($type === 'textarea')
            <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['rows' => 3, 'class' => 'w-full p-3 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none placeholder:text-outline '.($erreur ? 'ring-2 ring-error' : '')]) }}>{{ old($name, $value) }}</textarea>
        @elseif ($type === 'select')
            <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full '.($icone ? 'pl-10' : 'pl-3').' pr-10 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none appearance-none cursor-pointer '.($erreur ? 'ring-2 ring-error' : '')]) }}>{{ $slot }}</select>
            <span class="material-symbols-outlined absolute right-3 top-3 text-outline text-[18px] pointer-events-none">expand_more</span>
        @else
            <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" @if($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif
                   {{ $attributes->merge(['class' => 'w-full '.($icone ? 'pl-10' : 'px-3').' pr-3 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none placeholder:text-outline '.($erreur ? 'ring-2 ring-error' : '')]) }}/>
        @endif
    </div>
    @if ($erreur)
        <p class="font-body-sm text-body-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $erreur }}</p>
    @elseif ($aide)
        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $aide }}</p>
    @endif
</div>
