@props(['type' => 'boite', 'libelle' => null])
{{-- Pictogrammes des cartes médicaments (SVG inline des maquettes Stitch) --}}
@switch($type)
    @case('blister')
        <svg {{ $attributes->merge(['class' => 'w-20 h-20 text-amber-600']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#fef08a" height="32" rx="3" stroke="#d97706" stroke-width="2" width="44" x="10" y="16"></rect>
            @foreach ([[20, 26], [32, 26], [44, 26], [20, 38], [32, 38], [44, 38]] as [$cx, $cy])
                <circle cx="{{ $cx }}" cy="{{ $cy }}" fill="#ffffff" r="4" stroke="#d97706" stroke-width="1.5"></circle>
            @endforeach
        </svg>
        @break
    @case('gelule')
        <svg {{ $attributes->merge(['class' => 'w-20 h-20 text-[#16a34a]']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#bbf7d0" height="36" rx="4" stroke="#16a34a" stroke-width="2" width="36" x="14" y="14"></rect>
            <path d="M22 32h20" stroke="#16a34a" stroke-width="3"></path>
            <rect fill="#16a34a" height="16" rx="8" width="16" x="24" y="24"></rect>
        </svg>
        @break
    @case('tube')
        <svg {{ $attributes->merge(['class' => 'w-16 h-20 text-[#ea580c]']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#fed7aa" height="44" rx="4" stroke="#ea580c" stroke-width="2" width="20" x="22" y="10"></rect>
            <rect fill="#ea580c" height="6" rx="2" width="24" x="20" y="8"></rect>
            <circle cx="32" cy="24" fill="#ffffff" r="3"></circle>
            <circle cx="32" cy="34" fill="#ffffff" r="3"></circle>
            <circle cx="32" cy="44" fill="#ffffff" r="3"></circle>
        </svg>
        @break
    @case('lyoc')
        <svg {{ $attributes->merge(['class' => 'w-20 h-20 text-[#db2777]']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#fbcfe8" height="36" rx="4" stroke="#db2777" stroke-width="2" width="36" x="14" y="14"></rect>
            <circle cx="26" cy="32" fill="#ffffff" r="5" stroke="#db2777" stroke-width="1"></circle>
            <circle cx="38" cy="32" fill="#ffffff" r="5" stroke="#db2777" stroke-width="1"></circle>
        </svg>
        @break
    @case('ampoule')
        <svg {{ $attributes->merge(['class' => 'w-16 h-20 text-[#2563eb]']) }} fill="currentColor" viewBox="0 0 64 64">
            <path d="M30 8h4v8l3 4v28a3 3 0 01-3 3h-4a3 3 0 01-3-3V20l3-4V8z" fill="#bfdbfe" stroke="#2563eb" stroke-width="2"></path>
            <line stroke="#2563eb" stroke-width="2" x1="28" x2="36" y1="34" y2="34"></line>
        </svg>
        @break
    @case('flacon')
        <svg {{ $attributes->merge(['class' => 'w-16 h-20 text-[#0d9488]']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#99f6e4" height="34" rx="4" stroke="#0d9488" stroke-width="2" width="24" x="20" y="16"></rect>
            <path d="M28 10h8v6h-8z" fill="#0d9488"></path>
            <circle cx="32" cy="32" fill="#ffffff" r="3"></circle>
        </svg>
        @break
    @case('spray')
        <svg {{ $attributes->merge(['class' => 'w-16 h-20 text-slate-400']) }} fill="currentColor" viewBox="0 0 64 64">
            <path d="M22 14h16v24l6 6v6H20v-6l2-2V14z" fill="#cbd5e1" stroke="#64748b" stroke-width="2"></path>
            <rect fill="#64748b" height="6" width="12" x="24" y="8"></rect>
        </svg>
        @break
    @default
        <svg {{ $attributes->merge(['class' => 'w-20 h-20 text-[#0284c7]']) }} fill="currentColor" viewBox="0 0 64 64">
            <rect fill="#bae6fd" height="36" rx="4" stroke="#0284c7" stroke-width="2" width="40" x="12" y="14"></rect>
            <path d="M12 26h40" stroke="#0284c7" stroke-width="2"></path>
            <circle cx="32" cy="38" fill="#ffffff" r="7" stroke="#0284c7" stroke-width="1.5"></circle>
            <text fill="#0369a1" font-size="8" font-weight="bold" text-anchor="middle" x="32" y="41">{{ $libelle ?? 'Rx' }}</text>
        </svg>
@endswitch
