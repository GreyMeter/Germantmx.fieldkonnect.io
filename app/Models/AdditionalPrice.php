<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalPrice extends Model
{
    protected $fillable = [
        'price_id', 'model_name', 'model_id', 'price_adjustment'
    ];

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}

