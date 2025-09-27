<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostDechetRequest;
use App\Http\Requests\UpdatePostDechetRequest;
use App\Models\PostDechet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostDechetFrontController extends Controller
{
    // Liste publique
    public function index()
    {
        $posts = PostDechet::latest()->paginate(9);
        return view('frontoffice.postdechets.index', compact('posts'));
    }

    // Détails public
    public function show(PostDechet $postDechet)
    {
        return view('frontoffice.postdechets.show', compact('postDechet'));
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
                $paths[] = $file->store('posts', 'public');
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
                $paths[] = $file->store('posts', 'public');
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
