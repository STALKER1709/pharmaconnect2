<?php

namespace App\Http\Controllers;

use App\Events\NouveauMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessagerieController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where(fn ($q) => $q->where('client_id', $user->id)
                ->orWhere('pharmacie_user_id', $user->id)
                ->orWhere('livreur_user_id', $user->id))
            ->with(['client', 'pharmacie', 'livreur', 'commande', 'messages' => fn ($q) => $q->latest()->take(1)])
            ->orderByDesc('dernier_message_at')
            ->paginate(15);

        return view('messagerie.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        abort_unless($conversation->implique($request->user()->id), 403);

        $conversation->load(['client', 'pharmacie', 'livreur', 'commande.pharmacie']);

        $messages = $conversation->messages()->with('expediteur')->oldest()->get();

        $messages->where('expediteur_id', '!=', $request->user()->id)
            ->whereNull('lu_at')
            ->each(fn ($m) => $m->update(['lu_at' => now()]));

        return view('messagerie.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'interlocuteur' => $conversation->interlocuteurPour($request->user()->id),
            'monId' => $request->user()->id,
        ]);
    }

    /** Envoyer un message (broadcast Reverb + notification). */
    public function envoyer(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        abort_unless($conversation->implique($request->user()->id), 403);

        $validated = $request->validate([
            'contenu' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'expediteur_id' => $request->user()->id,
            'contenu' => $validated['contenu'],
        ]);

        $conversation->update(['dernier_message_at' => now()]);

        broadcast(new NouveauMessage($message))->toOthers();

        if ($request->wantsJson()) {
            return response()->json([
                'succes' => true,
                'message' => [
                    'id' => $message->id,
                    'contenu' => $message->contenu,
                    'expediteur_id' => $message->expediteur_id,
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ]);
        }

        return back()->with('succes', 'Message envoyé.');
    }

    /** Démarrer (ou retrouver) une conversation avec un utilisateur. */
    public function demarrer(Request $request, User $user): RedirectResponse
    {
        $moi = $request->user();
        abort_if($user->id === $moi->id, 422, 'Impossible de discuter avec soi-même.');

        // Conversation existante avec exactement cet interlocuteur ?
        $conversation = Conversation::query()
            ->where(fn ($q) => $q
                ->where(fn ($w) => $w->where('client_id', $moi->id)->where(fn ($x) => $x
                    ->where('pharmacie_user_id', $user->id)
                    ->orWhere('livreur_user_id', $user->id)))
                ->orWhere(fn ($w) => $w->where('client_id', $user->id)->where(fn ($x) => $x
                    ->where('pharmacie_user_id', $moi->id)
                    ->orWhere('livreur_user_id', $moi->id))))
            ->first();

        if (! $conversation && ($moi->estPharmacie() || $moi->estLivreur()) && ($user->estPharmacie() || $user->estLivreur())) {
            // Conversation inter-professionnels (ex. pharmacie ↔ livreur), sans client
            $conversation = Conversation::where(function ($q) use ($moi, $user) {
                $q->where('pharmacie_user_id', $moi->id)->where('livreur_user_id', $user->id);
            })->orWhere(function ($q) use ($moi, $user) {
                $q->where('pharmacie_user_id', $user->id)->where('livreur_user_id', $moi->id);
            })->first();
        }

        // Un admin n'est pas un participant possible ; on refuse proprement.
        abort_if($moi->estAdmin() || $user->estAdmin(), 422, 'La messagerie relie clients, pharmacies et livreurs.');

        if (! $conversation) {
            $data = ['client_id' => null, 'pharmacie_user_id' => null, 'livreur_user_id' => null];

            if ($moi->estClient()) {
                $data['client_id'] = $moi->id;
                if ($user->estPharmacie()) {
                    $data['pharmacie_user_id'] = $user->id;
                } elseif ($user->estLivreur()) {
                    $data['livreur_user_id'] = $user->id;
                }
            } elseif ($moi->estPharmacie()) {
                $data['pharmacie_user_id'] = $moi->id;
                $data['client_id'] = $user->estClient() ? $user->id : null;
                if ($user->estLivreur()) {
                    $data['livreur_user_id'] = $user->id;
                }
            } elseif ($moi->estLivreur()) {
                $data['livreur_user_id'] = $moi->id;
                $data['client_id'] = $user->estClient() ? $user->id : null;
                if ($user->estPharmacie()) {
                    $data['pharmacie_user_id'] = $user->id;
                }
            }

            $conversation = Conversation::create($data);
        }

        return redirect()->route('messagerie.show', $conversation);
    }

    /** Étendue : effectuer un appel (liens tel: via TelephonyGateway). */
    public function appel(Request $request, User $user): JsonResponse
    {
        $gateway = app(\App\Contracts\TelephonyGateway::class);

        return response()->json($gateway->appel($user->telephone ?? ''));
    }
}
