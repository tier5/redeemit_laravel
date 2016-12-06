<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class PasswordReset extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'password_resets';
}