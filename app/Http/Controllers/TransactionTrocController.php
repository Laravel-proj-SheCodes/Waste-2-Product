<?php

namespace App\Http\Controllers;

use App\Models\TransactionTroc;
use App\Models\OffreTroc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionTrocController extends Controller
{
public function index()
    {
        $transactions = TransactionTroc::with(['offreTroc.postDechet', 'offreTroc.user', 'utilisateurAcceptant'])->latest()->paginate(10);
        return view('backoffice.pages.transactions-troc.index', compact('transactions'));
    }

    /**
     * Display the specified resource (Backoffice).
     */
    public function show($id)
    {
        $transaction = TransactionTroc::with(['offreTroc.postDechet', 'offreTroc.user', 'utilisateurAcceptant'])->findOrFail($id);
        return view('backoffice.pages.transactions-troc.show', compact('transaction'));
    }

    // ... (tes autres méthodes backoffice stubs restent inchangées : create, store, edit, destroy)

    /**
     * Update the specified resource in storage (Backoffice).
     */
    public function update(Request $request, $id)
    {
        $transaction = TransactionTroc::findOrFail($id);
        // Vérifie autorisation (ex. : un des users impliqués)
        if (Auth::id() !== $transaction->offreTroc->user_id && Auth::id() !== $transaction->utilisateur_acceptant_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette transaction.');
        }

        $validated = $request->validate([
            'statut_livraison' => 'sometimes|in:en_cours,livre,annule',
            'evaluation_mutuelle' => 'sometimes|string|max:1000',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }
    
    /* =========================
     |  Frontend Methods
     * ========================= */

    /**
     * Index frontend: Liste des transactions de l'utilisateur connecté.
     */
    public function indexFront()
    {
        $transactions = TransactionTroc::where('utilisateur_acceptant_id', Auth::id())
            ->orWhereHas('offreTroc', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['offreTroc.postDechet', 'offreTroc.user', 'utilisateurAcceptant'])
            ->latest()
            ->paginate(10);

        return view('frontoffice.pages.transactions-troc.index', compact('transactions'));
    }

    /**
     * Show frontend: Détails d'une transaction.
     */
    public function showFront($id)
    {
        $transaction = TransactionTroc::with(['offreTroc.postDechet', 'offreTroc.user', 'utilisateurAcceptant'])->findOrFail($id);

        // Vérifie autorisation
        if (Auth::id() !== $transaction->offreTroc->user_id && Auth::id() !== $transaction->utilisateur_acceptant_id) {
            return redirect()->route('transactions-troc.index.front')->with('error', 'Vous n\'êtes pas autorisé à voir cette transaction.');
        }

        return view('frontoffice.pages.transactions-troc.show', compact('transaction'));
    }

    /**
     * Edit frontend: Formulaire pour updater statut livraison ou évaluation.
     */
    public function editFront($id)
    {
        $transaction = TransactionTroc::findOrFail($id);

        // Vérifie autorisation
        if (Auth::id() !== $transaction->offreTroc->user_id && Auth::id() !== $transaction->utilisateur_acceptant_id) {
            return redirect()->route('transactions-troc.index.front')->with('error', 'Vous n\'êtes pas autorisé à modifier cette transaction.');
        }

        return view('frontoffice.pages.transactions-troc.edit', compact('transaction'));
    }

    /**
     * Update frontend: Mettre à jour statut livraison ou évaluation.
     */
    public function updateFront(Request $request, $id)
    {
        $transaction = TransactionTroc::findOrFail($id);

        // Vérifie autorisation
        if (Auth::id() !== $transaction->offreTroc->user_id && Auth::id() !== $transaction->utilisateur_acceptant_id) {
            return redirect()->route('transactions-troc.index.front')->with('error', 'Vous n\'êtes pas autorisé à modifier cette transaction.');
        }

        $validated = $request->validate([
            'statut_livraison' => 'required|in:en_cours,livre,annule',
            'evaluation_mutuelle' => 'nullable|string|max:1000',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions-troc.show.front', $id)->with('success', 'Transaction mise à jour avec succès.');
    }
}