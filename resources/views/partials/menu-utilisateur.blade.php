{{-- Pastille avatar des maquettes (w-8 h-8 bg-primary + icône person) avec menu déroulant --}}
<div class="relative flex-shrink-0 ml-1" x-data="{ ouvert: false }">
    <button type="button" class="w-8 h-8 rounded-full bg-primary flex items-center justify-center" @click="ouvert = ! ouvert" aria-label="Mon compte">
        <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
    </button>
    <div class="absolute right-0 mt-2 w-60 bg-surface-container-lowest rounded-2xl border border-[#e2e8f0] shadow-[0px_12px_32px_-4px_rgba(30,41,59,0.08)] p-2 z-50"
         x-show="ouvert" x-cloak x-transition.opacity @click.outside="ouvert = false">
        @auth
            <div class="px-3 py-2 border-b border-slate-100 mb-1">
                <p class="font-label-lg text-label-lg text-on-surface truncate">{{ auth()->user()->name }}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">space_dashboard</span>Tableau de bord</a>
            <a href="{{ route('messagerie.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">chat</span>Messagerie</a>
            <a href="{{ route('chatbot.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">smart_toy</span>PharmaBot</a>
            <a href="{{ route('profil.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">manage_accounts</span>Mon profil</a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-tertiary hover:bg-tertiary-fixed/40"><span class="material-symbols-outlined text-[18px]">logout</span>Déconnexion</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">login</span>Connexion</a>
            <a href="{{ route('register') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl font-label-md text-label-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface"><span class="material-symbols-outlined text-[18px]">person_add</span>Créer un compte</a>
        @endauth
    </div>
</div>
