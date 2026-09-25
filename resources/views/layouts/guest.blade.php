<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-[#f0fdf6] font-body-md text-body-md text-on-surface antialiased">
    @include('partials.entete-public')

    <main class="w-full pt-20 bg-[#f0fdf6] min-h-[calc(100vh-20rem)]">
        <div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-center">
                <!-- Argumentaire (repris de l'accueil) -->
                <div class="hidden lg:flex lg:col-span-6 flex-col gap-space-lg pr-space-xl">
                    <div class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-[#dcfce7] border border-[#bbf7d0] text-[#15803d] font-label-md text-label-md shadow-sm">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#16a34a] animate-pulse"></span>
                        <span>Service pharmaceutique certifié à Douala &amp; Yaoundé</span>
                    </div>
                    <h2 class="font-headline-xl text-headline-xl text-slate-900 tracking-tight">
                        Vos médicaments, livrés à <span class="text-primary underline decoration-[#86efac] decoration-wavy decoration-2 underline-offset-8">Douala</span>
                    </h2>
                    <p class="font-body-lg text-body-lg text-slate-600 leading-relaxed">Commandez auprès des pharmacies agréées de votre quartier, payez par MTN MoMo ou Orange Money et suivez votre coursier en temps réel.</p>
                    <div class="flex flex-col gap-3">
                        @foreach ([
                            ['verified_user', 'bg-[#dcfce7] text-[#15803d]', 'Pharmacies agréées', "Médicaments authentiques issus de pharmaciens certifiés par l'Ordre National (ONPC)."],
                            ['phonelink_ring', 'bg-[#e0f2fe] text-[#0369a1]', 'Paiement Mobile Money', 'MTN MoMo et Orange Money, sans frais additionnels.'],
                            ['electric_moped', 'bg-[#fef3c7] text-[#b45309]', 'Livraison express < 45 min', 'Coursiers équipés de sacs isothermes scellés.'],
                        ] as [$icone, $couleurs, $titre, $texte])
                            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-[#e2e8f0] shadow-[0_4px_20px_-2px_rgba(22,163,74,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)] flex items-start gap-3.5">
                                <div class="w-11 h-11 rounded-xl {{ $couleurs }} flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[24px]">{{ $icone }}</span></div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-slate-900">{{ $titre }}</h3>
                                    <p class="mt-0.5 font-body-sm text-body-sm text-slate-600">{{ $texte }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Formulaire -->
                <div class="lg:col-span-6">
                    <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 border border-[#e2e8f0] shadow-[0_10px_30px_-5px_rgba(20,83,45,0.08)] max-w-xl mx-auto">
                        @yield('contenu')
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.pied-public')
    @include('partials.flash')
</body>
</html>
