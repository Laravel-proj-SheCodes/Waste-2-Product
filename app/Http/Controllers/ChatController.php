<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Proposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Ouvre (ou crée) la conversation pour une proposition ACCEPTÉE.
     * Retourne l'id de la conversation et un titre pour ta modale.
     */
    public function openFromProposition(Proposition $proposition)
    {
        $user = Auth::user();

        // relation post (compat: postDechet OU post)
        $post = $proposition->postDechet ?? $proposition->post;

        // sécurité: seuls owner du post OU auteur de la proposition
        abort_unless(
            $post && in_array($user->id, [$post->user_id, $proposition->user_id]),
            403
        );

        // accepte/accepted/acceptee/acceptée
        $accepted = in_array(strtolower((string)($proposition->statut ?? '')), ['accepte','accepted','acceptee','acceptée'], true);
        abort_unless($accepted, 403);

        $conv = Conversation::firstOrCreate(
            ['proposition_id' => $proposition->id],
            [
                'post_dechet_id' => $proposition->post_dechet_id,
                'owner_id'       => $post->user_id,
                'client_id'      => $proposition->user_id,
                'status'         => 'active',
            ]
        );

        return response()->json([
            'ok' => true,
            'conversation' => [
                'id'    => $conv->id,
                'title' => $post->titre ?? 'Conversation',
                'with'  => $user->id === $conv->owner_id
                            ? ($conv->client->name ?? 'Client')
                            : ($conv->owner->name ?? 'Propriétaire'),
            ],
        ]);
    }

    /**
     * Récupère les messages (option after=id pour pagination incrémentale).
     */
    public function messages(Conversation $conversation, Request $req)
    {
        abort_unless($conversation->hasParticipant(Auth::id()), 403);

        $afterId = (int) $req->query('after', 0);

        $q = $conversation->messages()
            ->with('user:id,name')
            ->orderBy('id');

        if ($afterId > 0) {
            $q->where('id', '>', $afterId);
        }

        $msgs = $q->limit(50)->get()->map(fn($m) => [
            'id'   => $m->id,
            'me'   => $m->user_id === Auth::id(),
            'user' => $m->user->name ?? 'User',
            'body' => $m->body,
            'at'   => $m->created_at?->diffForHumans(),
        ]);

        return response()->json(['ok' => true, 'messages' => $msgs]);
    }

    /**
     * Envoie un message dans la conversation.
     */
    public function send(Conversation $conversation, Request $req)
    {
        abort_unless($conversation->hasParticipant(Auth::id()), 403);

        $data = $req->validate([
            'body' => 'required|string|max:2000',
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'user_id'         => Auth::id(),
            'body'            => $data['body'],
        ]);

        return response()->json([
            'ok'      => true,
            'message' => [
                'id'   => $msg->id,
                'me'   => true,
                'user' => Auth::user()->name,
                'body' => $msg->body,
                'at'   => $msg->created_at?->diffForHumans(),
            ],
        ]);
    }
}
