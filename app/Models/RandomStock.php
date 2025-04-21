<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RandomStock extends Model
{
    use HasFactory;

    protected $table = 'random_stocks';

    protected $fillable = [
        'plant_id',
        'plant_name',
        'category_id',
        'random_cut',
        'stock',
        'created_at',
        'updated_at',
    ];

    public function plant()
    {
        return $this->belongsTo('App\Models\Plant', 'plant_id', 'id');
    }

    public function sizes()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }
}
