<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOutstanting extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'rate', 'customer_name', 'order_qty', 'days', 'dispatch', 'pending', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
