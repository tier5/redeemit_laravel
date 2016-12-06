<?php namespace App\Http\Controllers\Auth;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\PasswordReset;
use Hash;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

		$this->middleware('guest');
	}

	public function postEmail(Request $request){

		$this->validate($request, ['user_type'=> 'required', 'email' => 'required|email']);

		$user = User::where('email', $request->email)->where('type', $request->user_type)->first();
		if ($user) {
		   	// user found
		   	//dd($user);
			$user_email  = $user->email;
			$admin_email = "admin@mailinator.com";
			$user_name = "Reedemer Admin";
			$data = array('user_name' => $user_name, 'user_email' => $user_email);
			$token = chr(rand(65,90)).time().rand(99,99999).chr(rand(97,122));
			
			// we need to shift it inside of if
			PasswordReset::insert(['user_email' => $user_email, 'user_type' => $request->user_type, 'token' => $token]);

			if (\Mail::send('emails.nopartner', $data, function($message) use ($admin_email,$user_email,$user_name, $token){ 
				$subject="Click on this link to change your password. <a href='http://localhost/reedemer/admin/public/partner/resetpassword/".$token."'>http://localhost/reedemer/admin/public/partner/resetpassword/".$token."</a>";
				$message->from($admin_email, $user_name);
				$message->to($user_email)->subject($subject);
			}))
			{
				//PasswordReset::insert(['user_email' => $user_email, 'user_type' => 'partner', 'token' => $token]);
				return redirect()->back()->with('status', 'Reset Link is sent to your email.');
			} else {
				return redirect()->back()->withErrors(['email' => 'We could not sent you mail.']);
			}
		} else {
			return redirect()->back()->withErrors(['email' => 'Please check your email id.']);
		}
		
	}

	public function postReset(Request $request){
		//dd($request->all());
		$this->validate($request, ['password' => 'required|min:3|confirmed',
        	'password_confirmation' => 'required|min:3']);

	   	$user = User::where('email', $request->email)->where('type', $request->user_type)->first();
	    $user->password = Hash::make($request->password);
        $user->save();
        PasswordReset::where('user_email', $request->email)->where('user_type', $request->user_type)->delete();
        //return redirect('/auth/login');
        return redirect('/auth/login')->with('status', 'Your password has been changed successfully! Please login again.');

	}

}
