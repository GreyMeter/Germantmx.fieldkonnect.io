<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billet extends Model
{
    use HasFactory;

    // Optional: specify fillable fields
    protected $fillable = ['date', 'from_is', 'to_is', 'material', 'quantity', 'output', 'balance', 'rate', 'vehicle_no', 'remarks', 'created_by'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function to_name(){
        return $this->belongsTo('App\Models\Plant', 'to_is', 'id')->select('id','plant_name');
    }
}
