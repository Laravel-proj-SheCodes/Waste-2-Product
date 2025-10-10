<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostDechetRequest;
use App\Http\Requests\UpdatePostDechetRequest;
use App\Models\PostDechet;
use App\Models\Proposition;              // ✅ AJOUTER
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
$token = env('HUGGINGFACE_TOKEN');

class PostDechetFrontController extends Controller
{

//mouna job (troc)


// analyse IA pour les posts de type troc
public function analyze(Request $request)
{
    if (!$request->hasFile('photo')) {
        return response()->json(['error' => 'Aucune image reçue'], 400);
    }

    $file = $request->file('photo');
    $path = $file->store('analyze', 'public');

    $token = env('HUGGINGFACE_TOKEN');
    $imageData = base64_encode(file_get_contents($file->getRealPath()));

    try {
        // 1️⃣ Reconnaissance de l’objet
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
            'inputs' => $imageData
        ]);

        $result = $response->json();

        if (!isset($result[0]['label'])) {
            return response()->json(['error' => 'Analyse impossible', 'result' => $result]);
        }

        $label = $result[0]['label'];

        // 2️⃣ Générer un titre court et descriptif
        $titlePrompt = "Propose un titre court et précis pour un objet de type '$label' sur cette image.";

        $titleResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/gpt2', [
            'inputs' => $titlePrompt
        ]);

        $titleResult = $titleResponse->json();
        $generatedTitle = $titleResult[0]['generated_text'] ?? ucfirst($label);

        // 3️⃣ Générer une description détaillée incluant la couleur
        $descPrompt = "Décris en détail cet objet de type '$label' en mentionnant sa couleur dominante, son état, ses caractéristiques visibles et son usage.";

        $descResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/gpt2', [
            'inputs' => $descPrompt
        ]);

        $descResult = $descResponse->json();
        $detailedDesc = $descResult[0]['generated_text'] ?? "Objet: $label, couleur dominante visible, état bon et usage possible.";

        return response()->json([
            'success' => true,
            'titre' => $generatedTitle,
            'categorie' => $label,
            'etat' => 'neuf',          // valeur par défaut
            'unite_mesure' => 'pièce',     // valeur par défaut
            'quantite' => 1,               // valeur par défaut
            'description' => $detailedDesc
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur API : ' . $e->getMessage()]);
    }
}


    // Liste publique
    public function index()
    {
        $posts = PostDechet::latest()->paginate(9);
        return view('frontoffice.postdechets.index', compact('posts'));
    }

    // Détails public (+ propositions reçues si propriétaire)
    public function show(PostDechet $postDechet)
    {
        $received = collect();

        if (Auth::check() && Auth::id() === $postDechet->user_id) {
            $received = Proposition::with('user:id,name')
                ->where('post_dechet_id', $postDechet->id)
                ->latest()
                ->get();
        }

        return view('frontoffice.postdechets.show', compact('postDechet', 'received'));
    }

    // Création (auth requis)
    public function create()
    {
        return view('frontoffice.postdechets.create');
    }

    public function store(StorePostDechetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // Upload de 1..n photos (input name="photos[]")
        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $stored   = $file->store('posts', 'public');   // ex: posts/abc.jpg
                $paths[]  = str_replace('\\', '/', $stored);    // ✅ normalisation Windows
            }
            $data['photos'] = $paths;
        }

        $post = PostDechet::create($data);

        return redirect()
            ->route('front.waste-posts.show', $post)
            ->with('success', 'Post créé avec succès.');
    }

    // Edition (auth + propriétaire)
    public function edit(PostDechet $postDechet)
    {
        abort_if(Auth::id() !== $postDechet->user_id, 403);
        return view('frontoffice.postdechets.edit', compact('postDechet'));
    }

    public function update(UpdatePostDechetRequest $request, PostDechet $postDechet)
    {
        abort_if(Auth::id() !== $postDechet->user_id, 403);

        $data = $request->validated();

        if ($request->hasFile('photos')) {
            // supprime les anciennes si besoin
            if (is_array($postDechet->photos)) {
                foreach ($postDechet->photos as $p) {
                    Storage::disk('public')->delete($p);
                }
            }
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $stored   = $file->store('posts', 'public');
                $paths[]  = str_replace('\\', '/', $stored);     // ✅ pareil qu’au store
            }
            $data['photos'] = $paths;
        }

        $postDechet->update($data);

        return redirect()
            ->route('front.waste-posts.show', $postDechet)
            ->with('success', 'Post mis à jour.');
    }

    public function destroy(PostDechet $postDechet)
    {
        abort_if(Auth::id() !== $postDechet->user_id, 403);

        if (is_array($postDechet->photos)) {
            foreach ($postDechet->photos as $p) {
                Storage::disk('public')->delete($p);
            }
        }

        $postDechet->delete();

        return redirect()
            ->route('front.waste-posts.index')
            ->with('success', 'Post supprimé.');
    }
}
