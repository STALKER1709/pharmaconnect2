@props(['label', 'valeur', 'couleur' => 'text-emerald-700'])

<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5">
    <dt class="text-sm font-medium text-slate-500">{{ $label }}</dt>
    <dd class="mt-1 text-2xl font-semibold tracking-tight {{ $couleur }}">{{ $valeur }}</dd>
    @isset($detail)
        <p class="mt-1 text-xs text-slate-500">{{ $detail }}</p>
    @endisset
</div>
