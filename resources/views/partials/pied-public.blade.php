{{-- Pied de page public — identique aux maquettes Stitch --}}
<footer class="w-full bg-surface-container-lowest border-t border-[#e2e8f0] pt-space-xl pb-space-lg">
    <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-gutter-desktop mb-space-xl">
            <div class="lg:col-span-2 space-y-space-md pr-space-md">
                <div class="flex items-center gap-space-sm">
                    <img alt="PharmaConnect Logo" class="h-7 w-auto object-contain" src="{{ asset('images/logo-pharmaconnect.svg') }}"/>
                    <span class="font-headline-sm text-headline-sm text-primary tracking-tight">PharmaConnect</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-sm">Plateforme numérique sécurisée pour l'accès aux médicaments homologués, pharmacies de garde et services d'officine au Cameroun.</p>
                <div class="flex items-center gap-space-sm pt-space-xs">
                    <div class="inline-flex items-center gap-2 px-space-md py-1.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm"><span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span><span>Officines Agréées MINSANTE</span></div>
                </div>
            </div>
            <div>
                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Plateforme</h4>
                <ul class="space-y-space-sm">
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('accueil') }}#comment-ca-marche">À propos</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('public.pharmacies') }}">Pharmacies partenaires</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('accueil') }}#ordonnance">Téléverser une ordonnance</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('chatbot.index') }}">FAQ &amp; Aide</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Légal &amp; Sécurité</h4>
                <ul class="space-y-space-sm">
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#">Conditions Générales</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#">Politique de confidentialité</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#">Mentions Légales</a></li>
                    <li><a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#">Ordre des Pharmaciens</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Assistance locale</h4>
                <div class="space-y-space-sm font-body-md text-body-md text-on-surface-variant">
                    <div class="flex items-start gap-2"><span class="material-symbols-outlined text-primary text-[20px] mt-0.5">support_agent</span><div><p class="font-label-lg text-label-lg text-on-surface">Support Douala</p><p>+237 690 00 00 00</p></div></div>
                    <div class="flex items-start gap-2"><span class="material-symbols-outlined text-primary text-[20px] mt-0.5">chat</span><div><p class="font-label-lg text-label-lg text-on-surface">WhatsApp Officiel</p><p>+237 670 00 00 00</p></div></div>
                    <div class="flex items-start gap-2"><span class="material-symbols-outlined text-primary text-[20px] mt-0.5">schedule</span><p>Lun - Sam : 07h30 - 21h00</p></div>
                </div>
            </div>
        </div>
        <div class="pt-space-md border-t border-[#e2e8f0] flex flex-col md:flex-row items-center justify-between gap-space-md">
            <p class="font-body-sm text-body-sm text-on-surface-variant">© {{ now()->year }} PharmaConnect Cameroun. Tous droits réservés. Conforme aux directives de l'Ordre National des Pharmaciens du Cameroun (ONPC).</p>
            <div class="flex items-center gap-space-md text-on-surface-variant font-label-sm text-label-sm">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-primary text-[16px]">verified</span>Données Médicales Protégées</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-primary text-[16px]">local_shipping</span>Livraison Sécurisée Douala &amp; Yaoundé</span>
            </div>
        </div>
    </div>
</footer>
