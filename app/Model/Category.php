<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Category extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reedemer_category';


	public function parent()
    {
        return $this->belongsTo('App\Model\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Model\Category', 'parent_id')->where('visibility',1);
    }
 	
}