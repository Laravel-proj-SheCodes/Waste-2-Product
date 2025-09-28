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
            
            // Business logic validations
            if ($annonce->statut_annonce !== 'active') {
                $error = 'Cette annonce n\'est plus disponible';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => $error], 400);
                }
                return back()->with('error', $error);
            }

            if ($request->quantite > $annonce->postDechet->quantite) {
                $error = 'Quantité demandée supérieure au stock disponible';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => $error], 400);
                }
                return back()->with('error', $error);
            }

            // Prevent self-purchase
            if ($annonce->postDechet->user_id === Auth::id()) {
                $error = 'Vous ne pouvez pas commander votre propre annonce';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => $error], 400);
                }
                return back()->with('error', $error);
            }

            // Check for existing pending order
            $commandeExistante = Commande::where('annonce_marketplace_id', $request->annonce_marketplace_id)
                                       ->where('user_id', Auth::id())
                                       ->whereIn('statut_commande', ['en_attente', 'confirmee'])
                                       ->first();

            if ($commandeExistante) {
                $error = 'Vous avez déjà une commande en cours pour cette annonce';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => $error], 400);
                }
                return back()->with('error', $error);
            }

            $prixTotal = $annonce->prix * $request->quantite;

            $commande = Commande::create([
                'annonce_marketplace_id' => $request->annonce_marketplace_id,
                'user_id' => Auth::id(),
                'quantite' => $request->quantite,
                'prix_total' => $prixTotal,
                'statut_commande' => 'en_attente',
                'date_commande' => now()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($commande->load(['annonceMarketplace.postDechet', 'acheteur']), 201);
            }

            // Check if submission came from frontoffice
            if ($request->input('from_front')) {
                return redirect()->route('mes-commandes')->with('success', 'Commande passée avec succès!');
            }

            return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès');

        } catch (\Exception $e) {
            Log::error('Error creating order: ' . $e->getMessage());
            
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
    public function update(Request $request, Commande $commande)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        $request->validate([
            'statut_commande' => 'required|in:en_attente,confirmee,en_preparation,expediee,livree,annulee'
        ]);

        $userId = Auth::id();
        $isAcheteur = $commande->user_id === $userId;
        $isVendeur = $commande->annonceMarketplace->postDechet->user_id === $userId;

        if (!$isAcheteur && !$isVendeur) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return back()->with('error', 'Non autorisé');
        }

        // Status transition logic
        $nouveauStatut = $request->statut_commande;
        $statutActuel = $commande->statut_commande;

        $updateAllowed = false;

        // Only buyer can cancel if pending
        if ($nouveauStatut === 'annulee' && $isAcheteur && $statutActuel === 'en_attente') {
            $updateAllowed = true;
        }
        // Only seller can confirm, prepare, ship
        elseif ($isVendeur && in_array($nouveauStatut, ['confirmee', 'en_preparation', 'expediee'])) {
            $updateAllowed = true;
        }
        // Only buyer can mark as delivered
        elseif ($nouveauStatut === 'livree' && $isAcheteur && $statutActuel === 'expediee') {
            $updateAllowed = true;
        }

        if (!$updateAllowed) {
            $error = 'Transition de statut non autorisée';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $error], 400);
            }
            return back()->with('error', $error);
        }

        $commande->update(['statut_commande' => $nouveauStatut]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($commande->fresh());
        }

        return back()->with('success', 'Statut de la commande mis à jour avec succès');
    }

    /**
     * Remove the specified order
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

        $commande->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Commande supprimée avec succès']);
        }

        return redirect()->route('commandes.index')->with('success', 'Commande supprimée avec succès');
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
                           ->with(['annonceMarketplace.postDechet'])
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
