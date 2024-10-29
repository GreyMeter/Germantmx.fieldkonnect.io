<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [ 'customer_id','po_no','qty','unit_id','brand_id','category_id','base_price', 'discount_amt','soda_price','status','created_by', 'updated_by','deleted_at','created_at','updated_at'];

    public $timestamps = true;

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

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id');
    }

    public function order_confirm()
    {
        return $this->hasMany('App\Models\OrderConfirm', 'id', 'order_id');
    }

}
