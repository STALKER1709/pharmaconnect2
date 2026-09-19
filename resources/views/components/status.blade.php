@props(['statut', 'label' => null])

@php
    $classes = match (true) {
        in_array($statut, ['livree', 'reussi', 'actif', 'disponible']) => 'bg-emerald-100 text-emerald-800',
        in_array($statut, ['en_attente', 'initie']) => 'bg-amber-100 text-amber-800',
        in_array($statut, ['refusee', 'echoue', 'suspendu', 'annulee']) => 'bg-red-100 text-red-700',
        in_array($statut, ['confirmee', 'acceptee']) => 'bg-sky-100 text-sky-800',
        in_array($statut, ['prete', 'assignee']) => 'bg-indigo-100 text-indigo-800',
        in_array($statut, ['en_livraison', 'en_route']) => 'bg-cyan-100 text-cyan-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge $classes"]) }}>{{ $label ?? ucfirst(str_replace('_', ' ', $statut)) }}</span>
