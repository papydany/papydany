<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdgUser extends Model
{
    //
    protected $connection = 'mysql2';

      public function state()
    {
        return $this->belongsTo('App\Models\State');
    }
}
