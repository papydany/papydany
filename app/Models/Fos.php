<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fos extends Model
{
    //
      public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

       public function programme()
    {
        return $this->belongsTo('App\Models\Programme');
    }

    public function publish_results()
    {
        return $this->hasMany('App\Models\PublishResult');
    }

     // 
     public function specialization()
     {
         return $this->hasMany('App\Models\Specialization');
     }
}
