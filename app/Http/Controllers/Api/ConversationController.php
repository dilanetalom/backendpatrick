<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projets;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function getMessages(Projets $project)
    {
        $conversation = $project->conversation;
        if (!$conversation) {
            return response()->json([]);
        }
        return response()->json($conversation->messages()->with('user')->get());
    }

    public function sendMessage(Request $request, Projets $project)
    {
        $request->validate(['content' => 'required|string']);

        $conversation = $project->conversation;
        if (!$conversation) {
            $conversation = Conversation::create(['projet_id' => $project->id]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        

        // Diffusez l'événement
        event(new MessageSent(Auth::user(), $message));

        return response()->json($message->load('user'), 201);
    }


   /**
     * Affiche une conversation spécifique.
     * Cette méthode est ajoutée pour répondre à la route `resource`.
     *
     * @param \App\Models\Conversation $conversation
     * @return \Illuminate\Http\JsonResponse
     */
    // C'est ici que l'erreur se produit

    

}
