<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Product extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_product';

 	public function group()
	{
		return $this->hasOne('App\Model\Productgroup','id', 'product_group');
	}
    
}