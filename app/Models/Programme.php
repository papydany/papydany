<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    //

     public function fos()
    {
        return $this->hasMany('App\Models\Fos');
    }
}
