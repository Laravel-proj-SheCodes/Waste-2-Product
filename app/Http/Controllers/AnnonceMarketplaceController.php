<?php

namespace App\Http\Controllers;

use App\Models\AnnonceMarketplace;
use App\Models\PostDechet;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;



class AnnonceMarketplaceController extends Controller
{
    /**
     * Afficher toutes les annonces actives (API ou Web)
     */
    public function index(Request $request)
    {
        $query = AnnonceMarketplace::with(['postDechet.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('postDechet', function($subQuery) use ($search) {
                    $subQuery->where('titre', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
                })
                ->orWhereHas('postDechet.user', function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('statut_annonce', $request->status);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('prix', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('prix', '<=', $request->max_price);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['created_at', 'prix', 'statut_annonce'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $annonces = $query->paginate(4)->withQueryString();

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
    // Load relationships
    $annonce->load(['commandes.acheteur', 'postDechet']);
    
    // Calculate analytics data
    $analytics = [
        'total_orders' => $annonce->commandes->count(),
        'total_revenue' => $annonce->commandes->sum('prix_total'),
        'avg_revenue' => $annonce->commandes->avg('prix_total') ?? 0,
        'total_quantity' => $annonce->commandes->sum('quantite'),
        'avg_quantity' => $annonce->commandes->avg('quantite') ?? 0,
        'pending_orders' => $annonce->commandes->whereIn('statut_commande', ['en_attente', 'confirmee', 'en_preparation', 'expediee'])->count(),
        'delivered_orders' => $annonce->commandes->where('statut_commande', 'livree')->count(),
        'cancelled_orders' => $annonce->commandes->where('statut_commande', 'annulee')->count(),
        
        // Status distribution
        'status_distribution' => [
            'en_attente' => $annonce->commandes->where('statut_commande', 'en_attente')->count(),
            'confirmee' => $annonce->commandes->where('statut_commande', 'confirmee')->count(),
            'en_preparation' => $annonce->commandes->where('statut_commande', 'en_preparation')->count(),
            'expediee' => $annonce->commandes->where('statut_commande', 'expediee')->count(),
            'livree' => $annonce->commandes->where('statut_commande', 'livree')->count(),
            'annulee' => $annonce->commandes->where('statut_commande', 'annulee')->count(),
        ],
        
        // Revenue by day (last 7 days)
        'daily_revenue' => $annonce->commandes
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function($day) {
                return $day->sum('prix_total');
            }),
            
        // Top buyers
        'top_buyers' => $annonce->commandes->groupBy('acheteur_id')
            ->map(function($orders) {
                return [
                    'buyer' => $orders->first()->acheteur,
                    'total' => $orders->sum('prix_total'),
                    'orders' => $orders->count()
                ];
            })
            ->sortByDesc('total')
            ->take(5),
    ];
    
    return view('backoffice.pages.annonces.commandes', compact('annonce', 'analytics'));
}


public function exportOrders(AnnonceMarketplace $annonce)
{
    $annonce->load(['commandes.acheteur']);

    $filename = 'orders_annonce_' . $annonce->id . '_' . now()->format('Ymd_His') . '.csv';

    $response = new StreamedResponse(function() use ($annonce) {
        $handle = fopen('php://output', 'w');

        // En-têtes du CSV
        fputcsv($handle, [
            'Order ID', 'Buyer Name', 'Buyer Email', 
            'Quantity', 'Total Price (€)', 'Status', 'Date'
        ]);

        // Lignes des commandes
        foreach ($annonce->commandes as $commande) {
            fputcsv($handle, [
                $commande->id,
                $commande->acheteur->name ?? 'N/A',
                $commande->acheteur->email ?? '',
                $commande->quantite,
                number_format($commande->prix_total, 2),
                ucfirst(str_replace('_', ' ', $commande->statut_commande)),
                $commande->created_at?->format('d/m/Y H:i')
            ]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    return $response;
}



}

