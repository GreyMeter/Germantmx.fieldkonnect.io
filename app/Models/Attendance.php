<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [ 'active', 'user_id', 'punchin_date', 'punchin_time', 'punchin_longitude', 'punchin_latitude', 'punchin_address', 'punchin_image', 'punchout_date', 'punchout_time', 'punchout_latitude', 'punchout_longitude', 'punchout_address', 'punchout_image', 'punchin_summary', 'punchout_summary', 'worked_time', 'deleted_at', 'created_at', 'updated_at', 'working_type','attendance_status','remark_status'];
    
    public function users()
    {
        // return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','active','name','mobile','profile_image');
         return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
