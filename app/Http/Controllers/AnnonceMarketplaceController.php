<?php

namespace App\Http\Controllers;

use App\Models\AnnonceMarketplace;
use App\Models\PostDechet;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnnonceMarketplaceController extends Controller
{
    /**
     * Afficher toutes les annonces actives (API ou Web)
     */
    public function index(Request $request)
    {
        $annonces = AnnonceMarketplace::with(['postDechet.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => AnnonceMarketplace::count(),
            'active' => AnnonceMarketplace::where('statut_annonce', 'active')->count(),
            'vendue' => AnnonceMarketplace::where('statut_annonce', 'vendue')->count(),
            'revenue' => AnnonceMarketplace::where('statut_annonce', 'vendue')
                        ->join('commandes', 'annonce_marketplaces.id', '=', 'commandes.annonce_marketplace_id')
                        ->sum('commandes.prix_total') ?? 0
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($annonces);
        }

        return view('backoffice.pages.annonces.index', compact('annonces', 'stats'));
    }

    /**
     * Créer une nouvelle annonce
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'post_dechet_id' => 'required|exists:post_dechets,id',
            'prix' => 'required|numeric|min:0',
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        // Vérifier que le post_dechet appartient à l'utilisateur connecté
        $postDechet = PostDechet::where('id', $request->post_dechet_id)
                               ->where('user_id', Auth::id())
                               ->first();

        if (!$postDechet) {
            return response()->json(['error' => 'Post déchet non trouvé ou non autorisé'], 403);
        }

        // Vérifier qu'il n'y a pas déjà une annonce active
        $annonceExistante = AnnonceMarketplace::where('post_dechet_id', $request->post_dechet_id)
                                            ->where('statut_annonce', 'active')
                                            ->first();

        if ($annonceExistante) {
            return response()->json(['error' => 'Une annonce active existe déjà pour ce déchet'], 400);
        }

        $annonce = AnnonceMarketplace::create([
            'post_dechet_id' => $request->post_dechet_id,
            'prix' => $request->prix,
            'statut_annonce' => 'active'
        ]);

        return response()->json($annonce->load('postDechet'), 201);
    }

    /**
     * Afficher une annonce spécifique (API ou Web)
     */
    public function show(AnnonceMarketplace $annonce, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($annonce->load(['postDechet.user', 'commandes']));
        }

        $annonce->load(['postDechet.user', 'commandes']);
        return view('backoffice.pages.annonces.show', compact('annonce'));
    }

    /**
     * Mettre à jour une annonce (API ou Web)
     */
    public function update(Request $request, AnnonceMarketplace $annonce)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        // Vérifier que l'annonce appartient à l'utilisateur
        if ($annonce->postDechet->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->back()->with('error', 'Non autorisé');
        }

        $request->validate([
            'prix' => 'sometimes|numeric|min:0',
            'statut_annonce' => 'sometimes|in:active,inactive,vendue,expiree'
        ]);

        $annonce->update($request->only(['prix', 'statut_annonce']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($annonce->load('postDechet'));
        }

        return redirect()->route('annonces.index')->with('success', 'Annonce mise à jour avec succès');
    }

    /**
     * Supprimer une annonce (API ou Web)
     */
    public function destroy(AnnonceMarketplace $annonce, Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        // Vérifier que l'annonce appartient à l'utilisateur
        if ($annonce->postDechet->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->back()->with('error', 'Non autorisé');
        }

        // Vérifier qu'il n'y a pas de commandes en cours
        $commandesEnCours = $annonce->commandes()
                                  ->whereIn('statut_commande', ['en_attente', 'confirmee', 'en_preparation'])
                                  ->count();

        if ($commandesEnCours > 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Impossible de supprimer une annonce avec des commandes en cours'], 400);
            }
            return redirect()->back()->with('error', 'Impossible de supprimer une annonce avec des commandes en cours');
        }

        $annonce->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Annonce supprimée avec succès']);
        }

        return redirect()->route('annonces.index')->with('success', 'Annonce supprimée avec succès');
    }

    /**
     * Mes annonces (pour le vendeur)
     */
public function mesAnnonces(Request $request): JsonResponse
{
    if (!Auth::check()) {
        return response()->json(['error' => 'Authentification requise'], 401);
    }

    $toCurrency = $request->query('to', 'EUR');
    
    $annonces = AnnonceMarketplace::whereHas('postDechet', function($query) {
        $query->where('user_id', Auth::id());
    })->with(['postDechet', 'commandes'])->get();
    
    // Conversion de devise sécurisée avec gestion d'erreur
    try {
        $converter = new CurrencyConverter();
        
        $annonces->each(function ($annonce) use ($converter, $toCurrency) {
            if ($toCurrency !== 'EUR') {
                try {
                    $annonce->converted_price = $converter->convert($annonce->prix, $toCurrency);
                } catch (\Exception $e) {
                    Log::warning('Currency conversion failed', [
                        'error' => $e->getMessage(),
                        'annonce_id' => $annonce->id
                    ]);
                    $annonce->converted_price = $annonce->prix;
                }
            } else {
                $annonce->converted_price = $annonce->prix;
            }
            $annonce->display_currency = $toCurrency;
        });
    } catch (\Exception $e) {
        Log::error('CurrencyConverter initialization failed', ['error' => $e->getMessage()]);
        $annonces->each(function ($annonce) {
            $annonce->converted_price = $annonce->prix;
            $annonce->display_currency = 'EUR';
        });
    }
    
    return response()->json($annonces);
}

    /**
     * Changer le statut d'une annonce
     */
    public function updateStatut(Request $request, AnnonceMarketplace $annonce): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        // Vérifier que l'annonce appartient à l'utilisateur
        if ($annonce->postDechet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'statut_annonce' => 'required|in:active,inactive,vendue,expiree'
        ]);

        $annonce->update(['statut_annonce' => $request->statut_annonce]);

        return response()->json($annonce);
    }

public function getUserPostDechets(): JsonResponse
{
    Log::info('getUserPostDechets called. Authenticated: ' . (Auth::check() ? 'Yes' : 'No') . ', User ID: ' . (Auth::id() ?? 'None'));

    if (!Auth::check()) {
        Log::warning('Authentication failed for getUserPostDechets');
        return response()->json([
            'success' => false,
            'message' => 'Authentification requise'
        ], 401);
    }

    $userId = Auth::id();
    $postDechets = PostDechet::where('user_id', $userId)
        ->select('id', 'titre', 'localisation', 'description')
        ->get();

    Log::info("Retrieved postDechets for user $userId, count: " . $postDechets->count());

    return response()->json([
        'success' => true,
        'count'   => $postDechets->count(),
        'data'    => $postDechets
    ], 200);
}

public function showCommandes(AnnonceMarketplace $annonce)
{
    $annonce->load(['commandes.acheteur', 'commandes.vendeur', 'postDechet']);
    
    return view('backoffice.pages.annonces.commandes', compact('annonce'));
}

}
