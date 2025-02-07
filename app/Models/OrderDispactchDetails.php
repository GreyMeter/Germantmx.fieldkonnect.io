<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDispactchDetails extends Model
{
    use HasFactory;

    protected $fillable = [
         'order_dispatch_po_no' , 'driver_name' , 'driver_contact_number' , 'vehicle_number'
    ];

    public function dispactOrder(){
        return $this->hasMany(OrderDispactch::class , 'order_dispatch_po_no' , 'dispatch_po_no');
    }
}
