<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDispatch extends Model
{
    use HasFactory;

    protected $fillable = [ 'order_id','order_confirm_id','po_no','confirm_po_no','dispatch_po_no','qty','unit_id','brand_id','category_id','base_price','soda_price','rate','status','created_by', 'updated_by','deleted_at','created_at','updated_at'];

    public $timestamps = true;

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function order_confirm()
    {
        return $this->belongsTo('App\Models\OrderConfirm', 'order_confirm_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
