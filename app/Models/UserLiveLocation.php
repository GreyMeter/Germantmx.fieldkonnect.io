<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLiveLocation extends Model
{
    use HasFactory;

    protected $table = 'user_live_location';

    protected $fillable = [ 'active', 'userid', 'latitude', 'longitude', 'address', 'created_by', 'deleted_at', 'created_at', 'updated_at'];
}
