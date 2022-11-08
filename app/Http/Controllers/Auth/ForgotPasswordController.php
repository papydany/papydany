<?php
namespace App\Http\Controllers\Auth; 
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use App\Models\User; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
  
class ForgotPasswordController extends Controller
{
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showForgetPasswordForm()
      {
         //return view('auth.forgetPassword');
         return view('auth.forgot-password');
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitForgetPasswordForm(Request $request)
      {
          $request->validate([
              'username' => 'required',
          ]);
          $u = User::where('username',$request->input('username'))->first();
          $token = Str::random(64);
          if($u->email != null){
          DB::table('password_resets')->insert([
              'email' => $request->username, 
              'token' => $token, 
              'created_at' => Carbon::now()
            ]);
  
          Mail::send('emails.password_reset', ['token' => $token], function($message) use($u){
              $message->to($u->email);
              $message->subject('Reset Password');
          });

  
          return back()->with('message', 'We have e-mailed your password reset link!');
        }else{
          return back()->with('message', 'our have no email on your account. contact system admistrator!');
        }
      }
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showResetPasswordForm($token) { 
      
        $check = DB::table('password_resets')->where('token',$token)->first();
        if($check == null)
        {
            return redirect('/login')->with('message', 'Please the link has expired. please enter your username to reset again');
        }else{
         return view('auth.reset-password')->with('token',$token)->with('request',$check);
        }
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitResetPasswordForm(Request $request)
      {
          $request->validate([
              'username' => 'required',
              'password' => 'required|confirmed',
              'password_confirmation' => 'required'
          ]);
  
          $updatePassword = DB::table('password_resets')
                              ->where([
                                'email' => $request->username, 
                                'token' => $request->token
                              ])
                              ->first();
  
          if(!$updatePassword){
              return back()->withInput()->with('error', 'Invalid token!');
          }
  
          $user = User::where('username', $request->username)
                      ->update(['password' => Hash::make($request->password),'plain_password'=>$request->password]);
 
          DB::table('password_resets')->where(['email'=> $request->username])->delete();
  
          return redirect('/login')->with('message', 'Your password has been changed!');
      }
}