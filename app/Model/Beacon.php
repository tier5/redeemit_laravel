<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Beacon extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $guarded = array('id');
	protected $fillable = array();

	protected $table = 'reedemer_beacons';

	
   
 	
}