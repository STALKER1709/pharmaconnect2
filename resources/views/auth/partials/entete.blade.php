<div class="flex items-start gap-3 mb-space-lg">
    <div class="w-12 h-12 rounded-xl bg-[#dcfce7] text-[#15803d] flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-[26px]">{{ $icone }}</span>
    </div>
    <div>
        <h1 class="font-headline-md text-headline-md text-on-surface">{{ $titre }}</h1>
        @isset($sousTitre)<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">{{ $sousTitre }}</p>@endisset
    </div>
</div>
