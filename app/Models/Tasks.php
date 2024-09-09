<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [ 'active', 'user_id', 'title', 'descriptions', 'datetime', 'reminder', 'completed_at', 'completed', 'is_done', 'customer_id', 'status_id', 'created_by', 'created_at', 'updated_at'];
    
    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }
    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','profile_image','active','name','mobile');
    }
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
