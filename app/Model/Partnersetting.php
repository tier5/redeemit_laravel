<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Partnersetting extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_partner_settings';

 	/*public function reedemer()
	{
		return $this->belongsTo('App\User');
	}*/

	//public function reedemer()
	//{
	//	return $this->hasMany('App\User');
	//}

	//public function range() 
    //{
    //    return $this->hasOne('App\Price');
   // }

   // public function range() {
//	    return $this->hasOne-('App\Model\Price');
//	}
	

}