<?php
namespace App\Http\Controllers;

use App\Models\PostDechet;
use App\Models\Proposition;
use Illuminate\Http\Request;

class PropositionTrocController extends Controller
{
    public function index()
    {
        $posts = PostDechet::where('type_post', 'troc')->with('propositions')->latest()->paginate(10);
        return view('backoffice.pages.propositions-troc.index', compact('posts'));
    }

    public function create($postId)
    {
        $post = PostDechet::findOrFail($postId);
        if ($post->type_post !== 'troc') {
            return redirect()->back()->with('error', 'Ce post n\'est pas un troc.');
        }
        return view('backoffice.pages.propositions-troc.create', compact('post')); // Ajusté le chemin
    }

    public function store(Request $request, $postId)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $post = PostDechet::findOrFail($postId);
        if ($post->type_post !== 'troc') {
            return redirect()->back()->with('error', 'Ce post n\'est pas un troc.');
        }

        Proposition::create([
            'post_dechet_id' => $postId,
            'user_id' => auth()->id() ?? 1,
            'description' => $validated['description'],
            'date_proposition' => now(),
            'statut' => 'en_attente',
        ]);

        return redirect()->route('propositions-troc.index')->with('success', 'Proposition envoyée avec succès');
    }
}