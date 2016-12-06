<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Offer extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
    */
	protected $table = 'reedemer_offer';


    public function userOfferDetails()
    {
        return $this->belongsTo('App\Model\User','created_by','id');
    }
	
	public function inventorys()
    {
        return $this->hasOne('App\Model\Inventory');
    }

	  public function campaignDetails()
    {
        return $this->hasOne('App\Model\Campaign','id','campaign_id');
    }

    public function categoryDetails()
    {
        return $this->hasOne('App\Model\Category','id','cat_id')->select(array('id', 'cat_name'));
    }

    public function subCategoryDetails()
    {
        return $this->hasOne('App\Model\Category','id','subcat_id')->select(array('id', 'cat_name'));
    }
 
    public function partnerSettings()
    {
    	 return $this->hasOne('App\Model\Partnersetting','created_by','created_by');
    }
    
    public function myofferDetails()
    {
        return $this->hasMany('App\Model\UserBankOffer','offer_id','id')->select(array('offer_id', 'user_id','validate_within','validate_after'));
    }

    public function redemptions()
    {
        return $this->hasMany('App\Model\RedeemptionOffer','offer_id','id')->select(array('offer_id', 'user_id'));
    }
    
    
    public function offerDetail() {
        return $this->hasMany('App\Model\OfferDetail','offer_id','id');
    }

     public function companyDetail() {
        return $this->hasMany('App\Model\User','id','created_by')->select(array('id', 'company_name', 'address', 'city', 'state', 'zipcode', 'location', 'lat', 'lng', 'email','web_address','status','type'));
    }
	
	
    public function logoDetails() {
        return $this->hasOne('App\Model\Logo','reedemer_id','created_by')->where('default_logo', '1')->select(array('*'));
    }

   
 	
}