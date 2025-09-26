<?php
// app/Http/Controllers/CommandeController.php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\AnnonceMarketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    /**
     * Afficher toutes les commandes (admin)
     */
    public function index(): JsonResponse
    {
        $commandes = Commande::with(['annonceMarketplace.postDechet', 'acheteur'])
                           ->orderBy('date_commande', 'desc')
                           ->get();

        return response()->json($commandes);
    }

    /**
     * Créer une nouvelle commande
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'annonce_marketplace_id' => 'required|exists:annonce_marketplaces,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $annonce = AnnonceMarketplace::with('postDechet')->findOrFail($request->annonce_marketplace_id);
        
        // Vérifications métier
        if ($annonce->statut_annonce !== 'active') {
            return response()->json(['error' => 'Cette annonce n\'est plus disponible'], 400);
        }

        if ($request->quantite > $annonce->postDechet->quantite) {
            return response()->json(['error' => 'Quantité demandée supérieure au stock disponible'], 400);
        }

        // Empêcher l'auto-achat
        if ($annonce->postDechet->user_id === (Auth::id() ?? 1)) {
            return response()->json(['error' => 'Vous ne pouvez pas commander votre propre annonce'], 400);
        }

        // Vérifier si l'utilisateur a déjà une commande en attente pour cette annonce
        $commandeExistante = Commande::where('annonce_marketplace_id', $request->annonce_marketplace_id)
                                   ->where('user_id', Auth::id() ?? 1)
                                   ->whereIn('statut_commande', ['en_attente', 'confirmee'])
                                   ->first();

        if ($commandeExistante) {
            return response()->json(['error' => 'Vous avez déjà une commande en cours pour cette annonce'], 400);
        }

        $prixTotal = $annonce->prix * $request->quantite;

        $commande = Commande::create([
            'annonce_marketplace_id' => $request->annonce_marketplace_id,
            'user_id' => Auth::id() ?? 1,
            'quantite' => $request->quantite,
            'prix_total' => $prixTotal,
            'statut_commande' => 'en_attente',
            'date_commande' => now()
        ]);

        return response()->json($commande->load(['annonceMarketplace.postDechet', 'acheteur']), 201);
    }

    /**
     * Afficher une commande spécifique
     */
    public function show(Commande $commande): JsonResponse
    {
        // Vérifier les autorisations (acheteur ou vendeur)
        $userId = Auth::id() ?? 1;
        $isAcheteur = $commande->user_id === $userId;
        $isVendeur = $commande->annonceMarketplace->postDechet->user_id === $userId;

        if (!$isAcheteur && !$isVendeur) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json($commande->load(['annonceMarketplace.postDechet', 'acheteur']));
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function update(Request $request, Commande $commande): JsonResponse
    {
        $request->validate([
            'statut_commande' => 'required|in:en_attente,confirmee,en_preparation,expediee,livree,annulee'
        ]);

        $userId = Auth::id() ?? 1;
        $isAcheteur = $commande->user_id === $userId;
        $isVendeur = $commande->annonceMarketplace->postDechet->user_id === $userId;

        if (!$isAcheteur && !$isVendeur) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Logique de transition des statuts
        $nouveauStatut = $request->statut_commande;
        $statutActuel = $commande->statut_commande;

        // Seul l'acheteur peut annuler si en_attente
        if ($nouveauStatut === 'annulee' && $isAcheteur && $statutActuel === 'en_attente') {
            $commande->update(['statut_commande' => $nouveauStatut]);
        }
        // Seul le vendeur peut confirmer, préparer, expédier
        elseif ($isVendeur && in_array($nouveauStatut, ['confirmee', 'en_preparation', 'expediee'])) {
            $commande->update(['statut_commande' => $nouveauStatut]);
        }
        // Seul l'acheteur peut marquer comme livré
        elseif ($nouveauStatut === 'livree' && $isAcheteur && $statutActuel === 'expediee') {
            $commande->update(['statut_commande' => $nouveauStatut]);
        }
        else {
            return response()->json(['error' => 'Transition de statut non autorisée'], 400);
        }

        return response()->json($commande->fresh());
    }

    /**
     * Mes commandes (pour l'acheteur)
     */
    public function mesCommandes(): JsonResponse
    {
        $commandes = Commande::where('user_id', Auth::id() ?? 1)
                           ->with(['annonceMarketplace.postDechet'])
                           ->orderBy('date_commande', 'desc')
                           ->get();
        
        return response()->json($commandes);
    }

    /**
     * Commandes reçues (pour le vendeur)
     */
    public function commandesRecues(): JsonResponse
    {
        $commandes = Commande::whereHas('annonceMarketplace.postDechet', function($query) {
            $query->where('user_id', Auth::id() ?? 1);
        })->with(['annonceMarketplace.postDechet', 'acheteur'])
          ->orderBy('date_commande', 'desc')
          ->get();
        
        return response()->json($commandes);
    }
}