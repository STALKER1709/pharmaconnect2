<div class="flex items-center justify-between">
    <div>
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ $titre }}</h2>
        @isset($sousTitre)
            <p class="text-sm text-slate-500 mt-0.5">{{ $sousTitre }}</p>
        @endisset
    </div>
    {{ $actions ?? '' }}
</div>
