<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostDechet extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre','description','type_post','categorie','quantite','unite_mesure',
        'etat','localisation','user_id','date_publication','statut','photos'
    ];

    protected $casts = [
        'date_publication' => 'datetime',
        'photos'           => 'array',   // json -> array automatique
    ];

    // Expose automatiquement l'URL principale si le modèle est sérialisé en JSON
    protected $appends = ['main_photo_url'];

    /* =========================
     |        RELATIONS
     |=========================*/
    public function offreTrocs()
    {
        return $this->hasMany(OffreTroc::class, 'post_dechet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propositions()
    {
        return $this->hasMany(Proposition::class, 'post_dechet_id');
    }

    public function processusTransformations()
    {
        return $this->hasManyThrough(
            ProcessusTransformation::class,
            PropositionTransformation::class,
            'proposition_id',      // fk sur proposition_transformations -> vers proposition
            'dechet_entrant_id',   // fk sur processus_transformations -> vers post_dechets
            'id',                  // pk local post_dechets
            'id'                   // pk local proposition_transformations
        );
    }

    public function annonceMarketplace()
    {
        return $this->hasOne(AnnonceMarketplace::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'post_dechet_id', 'user_id')->withTimestamps();
    }

    /* =========================
     |   HELPERS / ACCESSORS
     |=========================*/

    /**
     * Normalise un chemin (retire 'public/', remplace '\' par '/', supprime le slash de début)
     */
    protected static function normalizePath(?string $p): ?string
    {
        if (!$p) return null;
        $p = str_replace('\\', '/', $p);
        $p = preg_replace('#^public/#', '', $p);
        return ltrim($p, '/');
    }

    /**
     * Accessor: URL de la photo principale (placeholder si manquante)
     */
    public function getMainPhotoUrlAttribute(): string
{
    $placeholder = asset('images/placeholder.jpg');

    // 1) récupérer la 1ère photo sous forme de string
    $first = null;
    $photos = $this->photos;

    if (is_array($photos)) {
        $first = $photos[0] ?? null;
    } elseif (is_string($photos) && $photos !== '') {
        $decoded = json_decode($photos, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $first = $decoded[0] ?? null;
        } else {
            $first = $photos;
        }
    }

    if (!$first) return $placeholder;

    // 2) normaliser
    $p = str_replace('\\', '/', $first);
    $p = preg_replace('#^public/#', '', $p);   // retire "public/"
    $p = ltrim($p, '/');

    // 3) cas déjà-URL ou déjà-/storage
    if (preg_match('#^https?://#i', $p)) {
        return $p; // URL absolue déjà valide
    }
    if (Str::startsWith($p, 'storage/')) {
        return asset($p); // ex: storage/posts/x.jpg
    }

    // 4) chemin relatif du disque public (ex: posts/x.jpg)
    if (Storage::disk('public')->exists($p)) {
        return Storage::disk('public')->url($p); // /storage/...
    }

    // 5) fallback si le fichier est physiquement sous public/storage
    if (file_exists(public_path('storage/'.$p))) {
        return asset('storage/'.$p);
    }

    // 6) dernier recours : placeholder
    return $placeholder;
}

    /**
     * Mutator: à l'enregistrement, normalise tous les chemins de 'photos'
     * - supprime "public/" au début
     * - remplace '\' par '/'
     */
    public function setPhotosAttribute($value): void
    {
        if (is_string($value)) {
            // Essayer de décoder si c'est un JSON
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = [$value];
            }
        }

        if (is_array($value)) {
            $value = array_values(array_filter(array_map(function ($p) {
                $p = is_string($p) ? self::normalizePath($p) : $p;
                return $p ?: null;
            }, $value)));
        } else {
            $value = [];
        }

        $this->attributes['photos'] = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    /* =========================
     |        SCOPES
     |=========================*/

    /**
     * Scope: recherche plein-texte simple (titre/description)
     */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string)$term);
        if ($term === '') return $query;

        return $query->where(function($q) use ($term) {
            $q->where('titre', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: applique les filtres de l'interface (q, categorie, localisation, etat)
     * Usage: PostDechet::withFilters(request()->all())->paginate(12);
     */
    public function scopeWithFilters($query, array $filters = [])
    {
        $q            = $filters['q'] ?? null;
        $categorie    = $filters['categorie'] ?? null;
        $localisation = $filters['localisation'] ?? null;
        $etat         = $filters['etat'] ?? null;

        $query->search($q);

        if (!empty($categorie)) {
            $query->where('categorie', $categorie);
        }

        if (!empty($localisation)) {
            // égalité stricte ; si tu veux "contient", remplace par like
            $query->where('localisation', $localisation);
            // ->where('localisation', 'like', "%{$localisation}%");
        }

        if (!empty($etat)) {
            $query->where('etat', $etat);
        }

        return $query;
    }
}