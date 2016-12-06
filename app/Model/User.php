<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class User extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $guarded=array();

	// protected $fillable = array('company_name', 'first_name','last_name','address','zipcode','email','web_address','password','cat_id','subcat_id','owner','create_offer_permission','status','approve','type','approve',);


	protected $hidden = array(
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    );

 	public function profile()
	{
		return $this->hasOne('App\Model\Logo','reedemer_id', 'id');
	}


 	public function offer()
	{
		return $this->hasManyThrough('App\Model\Offer', 'App\Model\Campaign', 'created_by', 'campaign_id');
	}	


	public function getUserId()
    {
        return $this->hasOne('App\Model\User','id', 'id')->select('id');
    }
  

	public function storeImage()
    {
        return $this->hasOne('App\Model\StoreImage','user_id', 'id');
    }
	 //self::deleting(function($user) { // before delete() method call this
        //     $user->photos()->delete();
             // do the rest of the cleanup...
      //  });
	//

}