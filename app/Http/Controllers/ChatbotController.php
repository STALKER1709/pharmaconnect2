<?php

namespace App\Http\Controllers;

use App\Models\ChatbotMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function index(Request $request): View
    {
        $messages = ChatbotMessage::where('user_id', $request->user()->id)
            ->orderBy('created_at')
            ->take(50)
            ->get();

        // Officines ouvertes en ce moment (encart « Gardes à proximité »)
        $pharmaciesOuvertes = \App\Models\Pharmacie::where('statut', 'actif')
            ->with(['horaires', 'user'])
            ->orderByDesc('note_moyenne')
            ->get()
            ->filter(fn ($p) => $p->estOuverte())
            ->take(2)
            ->values();

        return view('chatbot.index', compact('messages', 'pharmaciesOuvertes'));
    }

    /** Pose une question au chatbot (mock local par défaut). */
    public function demander(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $question = $validated['message'];

        $historique = ChatbotMessage::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'message' => $m->message])
            ->all();

        $reponse = app(\App\Contracts\ChatbotService::class)->conseiller($question, $historique);

        ChatbotMessage::create(['user_id' => $request->user()->id, 'role' => 'user', 'message' => $question]);
        ChatbotMessage::create(['user_id' => $request->user()->id, 'role' => 'assistant', 'message' => $reponse]);

        return response()->json([
            'reponse' => $reponse,
            'suggestions' => ['Fièvre et palu ?', 'Comment payer ?', 'Paracétamol : dose ?', 'Suivre ma livraison'],
        ]);
    }

    public function vider(Request $request): JsonResponse
    {
        ChatbotMessage::where('user_id', $request->user()->id)->delete();

        return response()->json(['succes' => true]);
    }
}
