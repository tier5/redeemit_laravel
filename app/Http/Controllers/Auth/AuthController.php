<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
//use App\Http\Requests\Request;
use Illuminate\Http\Request;
//use Illuminate\Http\Request; 


class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;		
		$this->middleware('guest', ['except' => 'getLogout']);
	}

	/* Login function overwrite */
	public function postLogin(Request $request)
	{

		$this->validate($request, [
			'email' => 'required', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password', 'user_type');

		if (
        ($this->auth->attempt(['username' => $request->get('email'), 'password' => $request->get('password')]))
        ||
        ($this->auth->attempt(['email' => $request->get('email'), 'password' => $request->get('password')]))
        )
		{	
			//dd($this->auth->user()->approve);
			if($this->auth->user()->approve == '1'){
				if($this->auth->user()->type == '1')		
				{	
					
					//return redirect()->intended( '/admin/dashboard' );
					return redirect('/admin/dashboard');
				}
				elseif($this->auth->user()->type == '2') 
				{
					
					//return redirect()->intended( '/user/dashboard' );
					return redirect('/user/dashboard');
				}
				else
				{
					$this->auth->logout();
					return redirect('/auth/login')
						->with('status', 'Your credentials does not match with any account! Please check your credentials.');
					
				}		
			} 
			else 
			{
				$this->auth->logout();
				return \Redirect::to('/auth/login')
					->with('status', 'Your account has been suspended by Redeemar Admin! Please contact Admin for more information.');
				
			}	
			
		}
		return redirect($this->loginPath())
			->withInput($request->only('email', 'remember'))
			->withErrors([
				'email' => $this->getFailedLoginMessage(),
				
			]);
	}	


	public function getLogout()
	{
		$this->auth->logout();
		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/auth/login');
	}

	
	
}
