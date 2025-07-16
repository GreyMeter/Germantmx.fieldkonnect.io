<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    use HasFactory;

    protected $fillable = ['plant_id', 'unit_id', 'plant_name', 'brand_id', 'category_id', 'stock', 'days', 'year', 'quarter', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function plant()
    {
        return $this->belongsTo('App\Models\Plant', 'plant_id', 'id');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }

    public function sizes()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function grades()
    {
        return $this->belongsTo('App\Models\UnitMeasure', 'unit_id', 'id');
    }

    public function created_by_name()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function updated_by_name()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
