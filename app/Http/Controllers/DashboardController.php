<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostDechet;
use App\Models\Proposition;
use App\Models\Donation;      // si le modèle existe
use App\Models\Transaction;   // si le modèle existe

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'users'         => class_exists(User::class) ? User::count() : 0,
            'wastePosts'    => class_exists(PostDechet::class) ? PostDechet::count() : 0,
            'trocPosts'     => class_exists(PostDechet::class) ? PostDechet::where('type_post','troc')->count() : 0,
            'proposals'     => class_exists(Proposition::class) ? Proposition::count() : 0,
            'donations'     => class_exists(Donation::class) ? Donation::count() : 0,
            'transactions'  => class_exists(Transaction::class) ? Transaction::count() : 0,
        ];

        $recentPosts = class_exists(PostDechet::class)
            ? PostDechet::latest()->take(5)->get(['id','titre','type_post','created_at'])
            : collect();

        $recentProps = class_exists(Proposition::class)
            ? Proposition::with('postDechet:id,titre')->latest()->take(5)->get(['id','post_dechet_id','statut','created_at'])
            : collect();

        $chart = [
            'labels' => ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'],
            'posts'  => [2,3,5,6,8,7,10,9,12,14,13,15],
            'troc'   => [1,1,2,3,4,5,6,5,6,7,8,9],
        ];

        return view('backoffice.pages.dashboard', compact('kpis','recentPosts','recentProps','chart'));
    }
}
