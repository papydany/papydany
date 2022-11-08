<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishResult extends Model
{
    public function fos()
    {
        return $this->belongsTo('App\Models\Fos');
    }
}
