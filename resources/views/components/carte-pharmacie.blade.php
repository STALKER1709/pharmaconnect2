@props(['pharmacie', 'miseEnAvant' => false])
{{-- Carte officine — maquette accueil (section « Pharmacies partenaires à Douala ») --}}
@php
    $ouverte = $pharmacie->relationLoaded('horaires') ? $pharmacie->estOuverte() : true;
    $horaire = $pharmacie->relationLoaded('horaires') ? $pharmacie->horaireDuJour(now()->dayOfWeek) : null;
    $h24 = $horaire?->estContinu() ?? false;
    $miseEnAvant = $miseEnAvant || $h24;
    $ombre = 'shadow-[0_4px_20px_-2px_rgba(22,163,74,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)]';
@endphp
<div class="bg-surface-container-lowest rounded-2xl p-6 border {{ $miseEnAvant ? 'border-[#bbf7d0]' : 'border-[#e2e8f0]' }} {{ $ombre }} flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-[0_10px_25px_-4px_rgba(20,83,45,0.08)]">
    <div>
        <div class="flex items-start justify-between gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#dcfce7] border border-[#bbf7d0] flex items-center justify-center text-[#15803d] flex-shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[28px]">local_pharmacy</span>
            </div>
            @if ($h24)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-900 font-label-sm text-label-sm font-bold">
                    <span>🌙 Garde 24h/24</span>
                </span>
            @elseif ($ouverte)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-label-sm text-label-sm font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @if ($horaire && $horaire->heure_fermeture)
                        Ouvert jusqu'à {{ str_replace('h00', 'h', $horaire->fermeture()) }}
                    @else
                        Ouvert maintenant
                    @endif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 font-label-sm text-label-sm font-semibold">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Fermé
                </span>
            @endif
        </div>
        <h3 class="mt-4 font-headline-sm text-headline-sm font-bold text-slate-900">{{ $pharmacie->nom }}</h3>
        <p class="font-body-sm text-body-sm text-slate-500 flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-[16px] text-slate-400">pin_drop</span>
            {{ collect([$pharmacie->adresse, $pharmacie->quartier, $pharmacie->ville])->filter()->unique()->implode(', ') }}
        </p>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-slate-700 font-body-sm text-body-sm">
            <div class="flex items-center gap-1 text-amber-500 font-medium">
                <span>★ {{ number_format((float) $pharmacie->note_moyenne, 1) }}</span>
                <span class="text-slate-400">({{ $pharmacie->nb_avis }} avis)</span>
            </div>
            <div class="inline-flex items-center gap-1 font-label-sm text-label-sm text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md font-medium">
                @if ($pharmacie->on_livraison)
                    <span>⏱️ 25-35 min</span>
                @else
                    <span>🏪 Retrait sur place</span>
                @endif
            </div>
        </div>
    </div>
    <div class="mt-6">
        @if ($miseEnAvant)
            <a class="w-full py-2.5 px-4 rounded-[12px] bg-[#16a34a] hover:bg-[#15803d] text-white font-label-lg text-label-lg font-semibold flex items-center justify-center gap-2 transition-all shadow-sm" href="{{ route('public.pharmacie', $pharmacie) }}">
                <span>Consulter l'officine</span>
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </a>
        @else
            <a class="w-full py-2.5 px-4 rounded-[12px] bg-white border border-[#bbf7d0] text-[#16a34a] hover:bg-[#f0fdf6] hover:border-[#16a34a] font-label-lg text-label-lg font-semibold flex items-center justify-center gap-2 transition-all" href="{{ route('public.pharmacie', $pharmacie) }}">
                <span>Consulter l'officine</span>
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </a>
        @endif
    </div>
</div>
