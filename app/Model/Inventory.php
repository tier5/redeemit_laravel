<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Inventory extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_inventory';

 	/*public function reedemer()
	{
		return $this->belongsTo('App\User');
	}*/

	//public function reedemer()
	//{
	//	return $this->hasMany('App\User');
	//}

	// public function offers()
 //    {
 //        return $this->belongsToMany('App\Model\Offer');
 //    }
    // public function offers()
    // {
    //     return $this->morphedByMany('App\Model\Offer', 'reedemer_offer_details')->withPivot('offer_id'

    //     	);
    // }
    public function offers() {
        return $this->belongsToMany('App\Model\Offer','reedemer_offer_details', 'offer_id', 'inventory_id');
    }
}