<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Action extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_action';

 	/*public function reedemer()
	{
		return $this->belongsTo('App\User');
	}*/

	// public function reedemer()
	// {
	// 	return $this->hasOne('App\User');
	// }

	// public function categoryDetails()
 //    {
 //        return $this->hasOne('App\Model\Category');
 //    }

}