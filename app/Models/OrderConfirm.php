<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderConfirm extends Model
{
    use HasFactory;

    protected $fillable = [ 'order_id','po_no','confirm_po_no','consignee_details', 'gst_number', 'delivery_address', 'supervisor_number','qty','unit_id','brand_id','category_id', 'material','loading_add','additional_rate','random_cut','special_cut','remark','base_price','soda_price','rate','status','created_by', 'updated_by','deleted_at','created_at','updated_at'];

    public $timestamps = true;

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function orderDispatch()
    {
        return $this->hasMany('App\Models\OrderDispatch', 'order_confirm_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
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

}
