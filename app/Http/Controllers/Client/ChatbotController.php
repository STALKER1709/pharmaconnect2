<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Contracts\ChatbotService;
use App\Models\ChatbotMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function index()
    {
        $messages = ChatbotMessage::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(50)
            ->get()
            ->reverse();

        return view('client.chatbot', ['messages' => $messages]);
    }

    /** Pose une question au chatbot et enregistre l'échange. */
    public function store(Request $request, ChatbotService $chatbot)
    {
        $donnees = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $historique = ChatbotMessage::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->map(fn (ChatbotMessage $m) => ['q' => $m->question, 'r' => $m->reponse])
            ->values()
            ->toArray();

        $reponse = $chatbot->repondre($donnees['question'], $historique);

        ChatbotMessage::create([
            'user_id' => Auth::id(),
            'question' => $donnees['question'],
            'reponse' => $reponse['reponse'],
        ]);

        return back()->with('succes', 'Le chatbot a répondu à votre question.');
    }
}
