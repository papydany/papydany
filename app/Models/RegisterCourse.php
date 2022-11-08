<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterCourse extends Model
{
    //
    public function assigncourse()
    {

        return $this->hasMany('App\Models\AssignCourse');
    }
}
