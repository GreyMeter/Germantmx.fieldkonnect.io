<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'brand_id', 'grade_id', 'zone_id', 'size_id', 'base_price'
    ];

    public function additionalPrices()
    {
        return $this->hasMany(AdditionalPrice::class);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\Category', 'size_id', 'id')->select('id','category_name','category_image');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id')->select('id','brand_name','brand_image');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\UnitMeasure', 'grade_id', 'id')->select('id','unit_name','unit_code');
    }

    public function zone()
    {
        return $this->belongsTo('App\Models\City', 'zone_id', 'id')->select('id','city_name');
    }
}

