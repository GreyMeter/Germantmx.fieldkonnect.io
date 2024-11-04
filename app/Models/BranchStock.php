<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'unit_name', 'division_id', 'stock', 'days', 'year', 'quarter', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function plant()
    {
        return $this->belongsTo('App\Models\Plant', 'unit_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id');
    }
}
