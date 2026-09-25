{{-- Messages flash : toast repris de la maquette d'accueil (#cartToast) --}}
@foreach (['succes' => ['check_circle', 'text-emerald-400'], 'erreur' => ['error', 'text-rose-400']] as $cle => [$icone, $couleur])
    @if (session($cle))
        <div class="fixed bottom-6 right-6 z-[60] transition-all duration-300"
             x-data="{ visible: true }" x-init="setTimeout(() => visible = false, 5000)"
             x-show="visible" x-transition.opacity>
            <div class="bg-slate-900 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 max-w-sm">
                <span class="material-symbols-outlined {{ $couleur }} text-[20px]">{{ $icone }}</span>
                <span class="font-body-sm text-body-sm">{{ session($cle) }}</span>
                <button type="button" class="ml-1 text-slate-400 hover:text-white" @click="visible = false" aria-label="Fermer">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>
    @endif
@endforeach
