<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['user_id', 'location', 'product_name', 'quantity', 'type', 'description', 'donation_date', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}