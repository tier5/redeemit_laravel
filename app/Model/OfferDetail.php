<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class OfferDetail extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_offer_details';

	
    public function inventoryDetails()
    {
        return $this->hasOne('App\Model\Inventory','id','inventory_id');
    }

    public function offerDetail()
    {
        return $this->hasOne('App\Model\Offer','id','offer_id');
    }
 	
}