<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class StoreImage extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'reedemer_store_images';

	protected $guarded=array();

	
	protected $hidden = array(
        'created_at',
        'updated_at'
    );

 	public function user()
	{
		return $this->belongsTo('App\Model\User','id', 'user_id');
	}

}