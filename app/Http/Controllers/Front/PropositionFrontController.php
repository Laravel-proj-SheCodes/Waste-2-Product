<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StorePropositionFrontRequest;
use App\Http\Requests\Front\UpdatePropositionFrontRequest;
use App\Models\PostDechet;
use App\Models\Proposition;
use Illuminate\Support\Facades\Auth;

class PropositionFrontController extends Controller
{
    public function index()
    {
        $propositions = Proposition::with('postDechet:id,titre')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('frontoffice.propositions.index', compact('propositions'));
    }

    public function create(PostDechet $postDechet)
    {
        abort_if($postDechet->user_id === Auth::id(), 403);
        return view('frontoffice.propositions.create', compact('postDechet'));
    }

    public function store(StorePropositionFrontRequest $request, PostDechet $postDechet)
    {
        abort_if($postDechet->user_id === Auth::id(), 403);

        Proposition::create([
            'post_dechet_id'   => $postDechet->id,
            'user_id'          => Auth::id(),
            'description'      => $request->validated()['description'],
            'date_proposition' => now(),
            'statut'           => 'en_attente',
        ]);

        return redirect()->route('front.propositions.index')
            ->with('success', 'Proposition envoyée.');
    }

    public function edit(Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);
        return view('frontoffice.propositions.edit', compact('proposition'));
    }

    public function update(UpdatePropositionFrontRequest $request, Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);

        $proposition->update($request->validated());

        return redirect()->route('front.propositions.index')
            ->with('success', 'Proposition mise à jour.');
    }

    public function destroy(Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);

        $proposition->delete();

        return back()->with('success', 'Proposition supprimée.');
    }

    public function show(PostDechet $postDechet)
{
    $received = collect();
    if (Auth::check() && Auth::id() === $postDechet->user_id) {
        $received = $postDechet->propositions()
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    return view('frontoffice.postdechets.show', compact('postDechet', 'received'));
}
}
