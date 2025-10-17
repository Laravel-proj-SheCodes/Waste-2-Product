<?php

// app/Models/Conversation.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model {
  protected $fillable = ['post_dechet_id','proposition_id','owner_id','client_id','status'];
  public function post(): BelongsTo { return $this->belongsTo(PostDechet::class,'post_dechet_id'); }
  public function proposition(): BelongsTo { return $this->belongsTo(Proposition::class); }
  public function owner(): BelongsTo { return $this->belongsTo(User::class,'owner_id'); }
  public function client(): BelongsTo { return $this->belongsTo(User::class,'client_id'); }
  public function messages(): HasMany {return $this->hasMany(Message::class);}
  public function hasParticipant($userId): bool {
    return $this->owner_id === $userId || $this->client_id === $userId;
  }
}