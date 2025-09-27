<?php

namespace App\Http\Controllers;

use App\Models\PostDechet;
use App\Http\Requests\StorePostDechetRequest;
use App\Http\Requests\UpdatePostDechetRequest;

class PostDechetController extends Controller
{
    public function index(){
        $posts = PostDechet::latest()->paginate(10);
        return view('backoffice.pages.postdechets.index', compact('posts'));
    }
    public function indexTroc()
    {
        $posts = PostDechet::where('type_post', 'troc')->latest()->paginate(10);
        // dd($posts->first()->photos); // Décommente pour déboguer si besoin
        return view('backoffice.pages.postdechets.troc-index', compact('posts'));
    }
        public function indexTrocFront()
    {
        $posts = PostDechet::where('type_post', 'troc')->latest()->paginate(10);
        // dd($posts->first()->photos); // Décommente pour déboguer si besoin
        return view('frontoffice.pages.postdechets.troc-index', compact('posts'));
    }
    public function create(){
        return view('backoffice.pages.postdechets.create');
    }

    public function store(StorePostDechetRequest $r){
        $data = $r->validated();
        $data['user_id'] = auth()->id() ?? 1;
        $data['date_publication'] = now();

        // upload multiple (optionnel)
        $files = [];
        if($r->hasFile('photos')){
            foreach($r->file('photos') as $f){ $files[] = $f->store('posts','public'); }
        }
        if($files) $data['photos'] = $files;

        PostDechet::create($data);
        return redirect()->route('postdechets.index')->with('ok','Post créé');
    }

    public function show(PostDechet $postdechet){
        $postdechet->load('propositions.user');
        return view('backoffice.pages.postdechets.show', compact('postdechet'));
    }

    public function edit(PostDechet $postdechet){
        return view('backoffice.pages.postdechets.edit', compact('postdechet'));
    }

    public function update(UpdatePostDechetRequest $r, PostDechet $postdechet){
        $data = $r->validated();
        if($r->hasFile('photos')){
            $files = $postdechet->photos ?? [];
            foreach($r->file('photos') as $f){ $files[] = $f->store('posts','public'); }
            $data['photos'] = $files;
        }
        $postdechet->update($data);
        return redirect()->route('postdechets.index')->with('ok','Post mis à jour');
    }

    public function destroy(PostDechet $postdechet){
        $postdechet->delete();
        return back()->with('ok','Post supprimé');
    }
    public function showOffres($post)
    {
        $post = PostDechet::with('offreTrocs')->findOrFail($post);
        $offres = $post->offreTrocs; // Relation avec OffreTroc
        return view('backoffice.pages.offres-troc.post-offres', compact('post', 'offres'));
    }
        public function showOffresFront($postId)
    {
        $post = PostDechet::with('offreTrocs')->findOrFail($postId);
        $offres = $post->offreTrocs;
        return view('frontoffice.pages.offres-troc.post-offres', compact('post', 'offres'));
    }

}
