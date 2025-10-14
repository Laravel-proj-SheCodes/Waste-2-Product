<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\AnnonceMarketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommandeController extends Controller
{
    /**
     * Display a listing of all orders (admin)
     */
  public function index(Request $request)
{
    $commandes = Commande::with(['annonceMarketplace.postDechet', 'acheteur'])
                       ->orderBy('date_commande', 'desc')
                       ->paginate(10);

    $stats = [
        'total' => Commande::count(),
        'en_attente' => Commande::where('statut_commande', 'en_attente')->count(),
        'confirmee' => Commande::where('statut_commande', 'confirmee')->count(),
        'livree' => Commande::where('statut_commande', 'livree')->count(),
    ];

    if ($request->wantsJson() || $request->ajax()) {
        return response()->json($commandes);
    }

    return view('backoffice.pages.commandes.index', compact('commandes', 'stats'));
}

    /**
     * Store a newly created order
     */
  public function store(Request $request)
{
    if (!Auth::check()) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }
        return redirect()->route('login')->with('error', 'Authentification requise');
    }

    $request->validate([
        'annonce_marketplace_id' => 'required|exists:annonce_marketplaces,id',
        'quantite' => 'required|integer|min:1',
    ]);

    try {
        $annonce = AnnonceMarketplace::with('postDechet')->findOrFail($request->annonce_marketplace_id);
        $post = $annonce->postDechet;

        // Vérifier statut et stock
        if ($annonce->statut_annonce !== 'active') {
            return back()->with('error', 'Cette annonce n\'est plus disponible');
        }

        if ($request->quantite > $post->quantite) {
            return back()->with('error', 'Quantité demandée supérieure au stock disponible');
        }

        // Empêcher auto-commande
        if ($post->user_id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas commander votre propre annonce');
        }

        // Vérifier commande existante en cours
        $commandeExistante = Commande::where('annonce_marketplace_id', $annonce->id)
            ->where('user_id', Auth::id())
            ->whereIn('statut_commande', ['en_attente', 'confirmee'])
            ->first();

        if ($commandeExistante) {
            return back()->with('error', 'Vous avez déjà une commande en cours pour cette annonce');
        }

        // Calcul du prix total
        $prixTotal = $annonce->prix * $request->quantite;

        // ✅ Créer la commande
        $commande = Commande::create([
            'annonce_marketplace_id' => $annonce->id,
            'user_id' => Auth::id(),
            'quantite' => $request->quantite,
            'prix_total' => $prixTotal,
            'statut_commande' => 'en_attente',
            'date_commande' => now()
        ]);

        // ✅ Diminuer la quantité du post associé
        $post->quantite -= $request->quantite;

        // Si la quantité atteint 0, désactiver l’annonce
        if ($post->quantite <= 0) {
            $post->quantite = 0;
            $annonce->statut_annonce = 'vendue';
        }

        // Sauvegarder les changements
        $post->save();
        $annonce->save();

        // ✅ Réponse adaptée
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($commande->load(['annonceMarketplace.postDechet', 'acheteur']), 201);
        }

        if ($request->input('from_front')) {
            return redirect()->route('mes-commandes')->with('success', 'Commande passée avec succès!');
        }

        return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès');

    } catch (\Exception $e) {
        Log::error('Erreur lors de la création de la commande : ' . $e->getMessage());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'Erreur lors de la création de la commande'], 500);
        }
        return back()->with('error', 'Erreur lors de la création de la commande');
    }
}


    /**
     * Display the specified order
     */
    public function show(Commande $commande, Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        // Check permissions (buyer or seller)
        $userId = Auth::id();
        $isAcheteur = $commande->user_id === $userId;
        $isVendeur = $commande->annonceMarketplace->postDechet->user_id === $userId;

        if (!$isAcheteur && !$isVendeur) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('commandes.index')->with('error', 'Non autorisé');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($commande->load(['annonceMarketplace.postDechet', 'acheteur']));
        }

        return view('backoffice.pages.commandes.show', compact('commande'));
    }

    /**
     * Update the specified order status
     */
public function update(Request $request, $id)
{
    $commande = Commande::with('annonceMarketplace.postDechet')->findOrFail($id);

    $request->validate([
        'statut_commande' => 'required|in:en_attente,confirmee,en_preparation,expediee,livree,annulee',
    ]);

    $ancienStatut = $commande->statut_commande;
    $nouveauStatut = $request->statut_commande;
    $annonce = $commande->annonceMarketplace;
    $post = $annonce->postDechet;
    $user = Auth::user();

    // ✅ CORRECTION: Utiliser les bons champs pour les permissions
    $isVendeur = $post->user_id === $user->id;
    $isAcheteur = $commande->user_id === $user->id;

    // Vérification d'accès
    if (!$isVendeur && !$isAcheteur) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cette commande.'], 403);
        }
        return back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
    }

    // L'acheteur ne peut que annuler et seulement si la commande est en attente
    if ($isAcheteur && !$isVendeur) {
        if (!($ancienStatut === 'en_attente' && $nouveauStatut === 'annulee') && 
            !($ancienStatut === 'expediee' && $nouveauStatut === 'livree')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Vous ne pouvez annuler que vos commandes en attente ou marquer comme livrées les commandes expédiées.'], 403);
            }
            return back()->with('error', 'Vous ne pouvez annuler que vos commandes en attente ou marquer comme livrées les commandes expédiées.');
        }
    }

    try {
        // ✅ Cas 1 : Annulation (acheteur ou vendeur)
        if (in_array($ancienStatut, ['en_attente', 'confirmee']) && $nouveauStatut === 'annulee') {
            $post->quantite += $commande->quantite;

            // Si le stock > 0 → on réactive l'annonce
            if ($post->quantite > 0 && $annonce->statut_annonce === 'vendue') {
                $annonce->statut_annonce = 'active';
            }
        }

        // ✅ Cas 2 : Réactivation d'une commande annulée (vendeur uniquement)
        if ($isVendeur && $ancienStatut === 'annulee' && in_array($nouveauStatut, ['en_attente', 'confirmee'])) {
            if ($commande->quantite > $post->quantite) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Stock insuffisant pour revalider cette commande.'], 400);
                }
                return back()->with('error', 'Stock insuffisant pour revalider cette commande.');
            }

            $post->quantite -= $commande->quantite;
            if ($post->quantite <= 0) {
                $post->quantite = 0;
                $annonce->statut_annonce = 'vendue';
            }
        }

        // ✅ Mise à jour du statut
        $commande->update(['statut_commande' => $nouveauStatut]);

        $post->save();
        $annonce->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Statut de la commande mis à jour avec succès.',
                'commande' => $commande->load(['annonceMarketplace.postDechet', 'acheteur'])
            ]);
        }

        return back()->with('success', 'Statut de la commande mis à jour avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur update commande : ' . $e->getMessage());
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de la commande.'], 500);
        }
        return back()->with('error', 'Erreur lors de la mise à jour de la commande.');
    }
}



    /**
    
     * ⚠️  Restaurer le stock lors de la suppression
     */
    public function destroy(Commande $commande, Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $isAcheteur = $commande->user_id === $userId;
        $isVendeur = $commande->annonceMarketplace->postDechet->user_id === $userId;

        if (!$isAcheteur && !$isVendeur) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('commandes.index')->with('error', 'Non autorisé');
        }

        // Only allow deletion if order is pending or cancelled
        if (!in_array($commande->statut_commande, ['en_attente', 'annulee'])) {
            $error = 'Impossible de supprimer une commande en cours de traitement';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $error], 400);
            }
            return back()->with('error', $error);
        }

        try {
            // ✅ CORRECTION : Restaurer le stock si la commande n'était pas annulée
            if ($commande->statut_commande !== 'annulee') {
                $post = $commande->annonceMarketplace->postDechet;
                $annonce = $commande->annonceMarketplace;
                
                $post->quantite += $commande->quantite;
                
                // Réactiver l'annonce si elle était vendue
                if ($annonce->statut_annonce === 'vendue') {
                    $annonce->statut_annonce = 'active';
                    $annonce->save();
                }
                
                $post->save();
            }

            $commande->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Commande supprimée avec succès']);
            }

            return redirect()->route('commandes.front')->with('success', 'Commande supprimée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression commande : ' . $e->getMessage());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Erreur lors de la suppression'], 500);
            }
            return back()->with('error', 'Erreur lors de la suppression');
        }
    }
    /**
     * Display user's orders (buyer perspective)
     */
    public function mesCommandes(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        $commandes = Commande::where('user_id', Auth::id())
                           ->with(['annonceMarketplace.postDechet.user'])
                           ->orderBy('date_commande', 'desc')
                           ->get();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($commandes);
        }

        return view('frontoffice.pages.commandes.mes-commandes', compact('commandes'));
    }

    /**
     * Display received orders (seller perspective)
     */
    public function commandesRecues(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        $commandes = Commande::whereHas('annonceMarketplace.postDechet', function($query) {
            $query->where('user_id', Auth::id());
        })->with(['annonceMarketplace.postDechet', 'acheteur'])
          ->orderBy('date_commande', 'desc')
          ->get();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($commandes);
        }

        return view('frontoffice.pages.commandes.commandes-recues', compact('commandes'));
    }

    /**
     * Frontoffice landing page for orders
     */
    public function frontLanding()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Authentification requise');
        }

        $mesCommandes = $this->getMesCommandesData();
        $commandesRecues = $this->getCommandesRecuesData();
        
        return view('frontoffice.pages.commandes.commandes', compact('mesCommandes', 'commandesRecues'));
    }

    /**
     * Get user's orders data for frontoffice
     */
    protected function getMesCommandesData()
    {
        return Commande::where('user_id', Auth::id())
                     ->with(['annonceMarketplace.postDechet'])
                     ->orderBy('date_commande', 'desc')
                     ->get();
    }

    /**
     * Get received orders data for frontoffice
     */
    protected function getCommandesRecuesData()
    {
        return Commande::whereHas('annonceMarketplace.postDechet', function($query) {
            $query->where('user_id', Auth::id());
        })->with(['annonceMarketplace.postDechet', 'acheteur'])
          ->orderBy('date_commande', 'desc')
          ->get();
    }

    /**
     * Quick status update for orders
     */
    public function updateStatut(Request $request, Commande $commande)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        $request->validate([
            'statut' => 'required|in:en_attente,confirmee,en_preparation,expediee,livree,annulee'
        ]);

        // Use the same logic as update method
        return $this->update($request->merge(['statut_commande' => $request->statut]), $commande);
    }
}




