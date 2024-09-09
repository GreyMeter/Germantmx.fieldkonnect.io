<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimarySales extends Model
{
    use HasFactory;

    protected $table = 'primary_sales';

    protected $fillable = [
        'active',
        'invoiceno',
        'invoice_date',
        'month',
        'division',
        'dealer',
        'customer_id',
        'branch',
        'city',
        'state',
        'final_branch',
        'sales_person',
        'emp_code',
        'model_name',
        'product_name',
        'quantity',
        'rate',
        'net_amount',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'new_group',
        'store_name',
        'group_name',
        'new_group_name',
        'product_id',
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'employee_codes');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
}
