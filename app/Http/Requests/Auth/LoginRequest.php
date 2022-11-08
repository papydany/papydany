<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
       return [
            'username' => 'required',
            'password' => 'required|string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request)
    {
       // dd();
        $this->ensureIsNotRateLimited();

        //if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                // Authentication was successful...
            
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        if(auth()->user()->status >= 1)
        {
            Auth::logout();
            throw ValidationException::withMessages([
                '','','your account has been disabled. contact the system adminstrator'
            ]);
           
         return back();
        }
         $user_id = auth()->user()->id;
         DB::table('users')
         ->where('id',$user_id)
         ->update(['resultRight' => 0]);
         $role = DB::table('roles')
                ->join('user_roles', 'user_roles.role_id', '=', 'roles.id')
                ->Where('user_roles.user_id',$user_id)
                ->select('roles.name')
                ->first();
                $request->session()->put('key', $role);
                if(auth()->user()->edit_right > 0)
                {
                   
                 $new_e =auth()->user()->edit_right - 1;
    
                 $u =User::find($user_id);
                 $u->edit_right =$new_e;
                 $u->save();
                }    
                
                return redirect('/'); 
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('username')).'|'.$this->ip();
    }
}
