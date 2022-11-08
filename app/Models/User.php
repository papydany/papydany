<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','username','plain_password','faculty_id','department_id','fos_id','programme_id','edit_right',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

      /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
 public function assigncourse()
    {
return $this->hasMany('App\Models\AssignCourse');
    }

    

     public function roles()
    {
        return $this->belongsToMany('App\Models\Role','user_roles','user_id', 'role_id');
    }

   /* public function hasAnyRole($roles)
    {
        if(is_array($roles))
        {
            foreach ($roles as $key => $value) {
               if($this->hasRole($value))
               {
                return true;
               }
            }
        }else{
         if($this->hasRole($roles))
               {
                return true;
               }
        }
        return false;
    }*/

    public function hasRole($role,$role2,$role3,$role4,$role5)
    {
        if($this->roles()->whereIn('name',[$role,$role2,$role3,$role4,$role5])->first())
        {
            return true;
        }

        return false;
    }
}
