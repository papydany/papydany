<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    public function fos()
    {
        return $this->belongsTo('App\Models\Fos');
    }

      
}
