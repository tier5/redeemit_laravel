<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Campaign extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_campaign';

 	public function offer()
	{
		return $this->hasMany('App\Model\Offer');
	}

	//public function reedemer()
	//{
	//	return $this->hasMany('App\User');
	//}

}