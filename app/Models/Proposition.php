<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_dechet_id',
        'user_id',
        'description',
        'date_proposition',
        'statut',
    ];

    protected $casts = [
        'date_proposition' => 'date',
    ];

    public function postDechet()
    {
        return $this->belongsTo(PostDechet::class, 'post_dechet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
