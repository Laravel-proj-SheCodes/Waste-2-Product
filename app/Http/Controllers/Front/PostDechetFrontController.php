<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostDechetRequest;
use App\Http\Requests\UpdatePostDechetRequest;
use App\Models\PostDechet;
use App\Models\Proposition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostDechetFrontController extends Controller
{
    // =========================
    //       IA (Troc)
    // =========================

    // Analyse IA pour les posts de type troc (photo -> label, titre, description)
    public function analyze(Request $request)
    {
        if (!$request->hasFile('photo')) {
            return response()->json(['error' => 'Aucune image reçue'], 400);
        }

        $file = $request->file('photo');
        $file->store('analyze', 'public'); // on n’utilise pas le path après

        $token     = env('HUGGINGFACE');
        $imageData = base64_encode(file_get_contents($file->getRealPath()));

        try {
            // 1) Reconnaissance d’objet
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
                'inputs' => $imageData
            ]);

            $result = $response->json();
            if (!isset($result[0]['label'])) {
                return response()->json(['error' => 'Analyse impossible', 'result' => $result], 422);
            }

            $label = $result[0]['label'];

            // 2) Génération d’un titre
            $titlePrompt = "Propose un titre court et précis pour un objet de type '$label' sur cette image.";
            $titleResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/gpt2', [
                'inputs' => $titlePrompt
            ]);
            $titleResult    = $titleResponse->json();
            $generatedTitle = $titleResult[0]['generated_text'] ?? ucfirst($label);

            // 3) Génération d’une description
            $descPrompt = "Décris en détail cet objet de type '$label' en mentionnant sa couleur dominante, son état, ses caractéristiques visibles et son usage.";
            $descResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/gpt2', [
                'inputs' => $descPrompt
            ]);
            $descResult   = $descResponse->json();
            $detailedDesc = $descResult[0]['generated_text'] ?? "Objet: $label, couleur dominante visible, état bon et usage possible.";

            return response()->json([
                'success'       => true,
                'titre'         => $generatedTitle,
                'categorie'     => $label,
                'etat'          => 'neuf',
                'unite_mesure'  => 'pièce',
                'quantite'      => 1,
                'description'   => $detailedDesc
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur API : ' . $e->getMessage()], 500);
        }
    }

    // =========================
    //      LISTE / DÉTAIL
    // =========================

    // Liste publique avec filtres (q, categorie, localisation, etat)
    public function index(Request $request)
    {
        // Filtres entrés par l’utilisateur (les 4 du formulaire)
        $filters = $request->only(['q', 'categorie', 'localisation', 'etat']);

        // Requête principale
        $posts = PostDechet::query()
            // ->where('statut', 'approved') // si tu utilises un statut de modération
            ->withFilters($filters)          // scopes du modèle
            ->latest()
            ->paginate(12)
            ->withQueryString();             // conserve les paramètres dans la pagination

        // Remplir la liste Catégorie pour le <select>
        $categories = PostDechet::query()
            ->select('categorie')
            ->whereNotNull('categorie')
            ->where('categorie', '!=', '')
            ->distinct()
            ->orderBy('categorie')
            ->pluck('categorie');

        return view('frontoffice.postdechets.index', compact('posts', 'categories'));
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

    // =========================
    //          CRUD
    // =========================

    // Création (auth requis)
    public function create()
    {
        return view('frontoffice.postdechets.create');
    }

    public function store(StorePostDechetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // Upload 1..n photos (name="photos[]")
        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $stored  = $file->store('posts', 'public');   // ex: posts/abc.jpg
                $paths[] = str_replace('\\', '/', $stored);   // normalisation Windows
            }
            $data['photos'] = $paths; // le mutator du modèle normalisera encore si besoin
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
            // Option : supprimer les anciennes (seulement si logique voulue)
            if (is_array($postDechet->photos)) {
                foreach ($postDechet->photos as $p) {
                    Storage::disk('public')->delete($p);
                }
            }
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $stored  = $file->store('posts', 'public');
                $paths[] = str_replace('\\', '/', $stored);
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

    // =========================
    //     IA : Estimation €
    // =========================
    public function estimateAI(PostDechet $postDechet)
    {
        // Vérifie qu'il y a au moins une photo exploitable
        $photoPath = $postDechet->photos[0] ?? null;
        if (!$photoPath || !Storage::disk('public')->exists($photoPath)) {
            return response()->json(['error' => 'Aucune image disponible pour estimation'], 400);
        }

        $filePath  = Storage::disk('public')->path($photoPath);
        $imageData = base64_encode(file_get_contents($filePath));
        $token     = env('HUGGINGFACE');

        try {
            // 1) Reconnaissance
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
                'inputs' => $imageData
            ]);

            $result = $response->json();
            $label  = $result[0]['label'] ?? null;

            if (!$label) {
                return response()->json(['error' => 'Objet non reconnu.'], 400);
            }

            // 2) Table de prix simple
            $prixTable = [
                // Furniture
                'sofa' => 1500, 'chair' => 150, 'table' => 300, 'bed' => 800,
                'lamp' => 80, 'desk' => 200, 'studio couch, day bed' => 1200,
                // Vehicles
                'car' => 20000, 'bicycle' => 500, 'motorbike' => 3000,
                // Electronics
                'laptop' => 2500, 'smartphone' => 1200, 'tablet' => 900,
                'television' => 1500, 'headphones' => 200,
                // Toys
                'toy' => 50, 'board game' => 80, 'robot' => 400, 'doll' => 60,
                // Misc
                'book' => 30, 'bag' => 150, 'shoes' => 100, 'watch' => 300, 'clothing' => 200,
            ];

            $estimatedPrice = $prixTable[strtolower($label)] ?? 100;

            // 3) Ajustement selon l’état
            $etat       = strtolower($postDechet->etat ?? 'neuf');
            $etatFactor = match ($etat) {
                'neuf' => 1.0,
                'bon'  => 0.7,
                'usé'  => 0.4,
                default => 1.0,
            };
            $estimatedPrice = round($estimatedPrice * $etatFactor, 2);

            // Si besoin pour l’affichage (non persistant)
            $postDechet->estimated_price = $estimatedPrice;

            return response()->json([
                'success'         => true,
                'categorie'       => $label,
                'estimated_price' => $estimatedPrice
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur IA : ' . $e->getMessage()], 500);
        }
    }

    // =========================
    //  Recherche visuelle (IA)
    // =========================
    public function visualSearch(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // 5 Mo
        ]);

        $file      = $request->file('photo');
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $token     = env('HUGGINGFACE');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
                'inputs' => $imageData
            ]);

            $result = $response->json();
            $label  = $result[0]['label'] ?? null;

            if (!$label) {
                return redirect()->back()->with('error', 'Objet non reconnu.');
            }

            // Recherche de posts similaires par catégorie détectée
            $posts = PostDechet::where('categorie', 'like', "%{$label}%")
                ->latest()
                ->paginate(9)
                ->withQueryString();

            // NOTE : adapte le chemin de vue si nécessaire
            return view('frontoffice.pages.postdechets.troc-index', [
                'posts'       => $posts,
                'searching'   => true,
                'searchLabel' => $label
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur IA : ' . $e->getMessage());
        }
    }
}
