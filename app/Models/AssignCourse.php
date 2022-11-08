<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignCourse extends Model
{
    //

     public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

      public function reg_course()
    {
    	return $this->belongsTo('App\Models\RegisterCourse','registercourse_id');
    }
}
