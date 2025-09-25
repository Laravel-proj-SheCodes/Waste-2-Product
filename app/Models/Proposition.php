<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = ['post_dechet_id','user_id','description','date_proposition','statut'];

    protected $casts = ['date_proposition' => 'datetime'];

    public function postDechet(){ return $this->belongsTo(PostDechet::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
