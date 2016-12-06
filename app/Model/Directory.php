<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Directory extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_repository';

 	/*public function reedemer()
	{
		return $this->belongsTo('App\User');
	}*/

	//public function reedemer()
	//{
	//	return $this->hasMany('App\User');
	//}

}